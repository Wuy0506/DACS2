<?php
/**
 * Staff Repair Request Model
 * Xử lý các chức năng quản lý yêu cầu sửa chữa dành cho Staff
 */

require_once __DIR__ . '/../database.php';

class StaffRepairModel {
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
        
        if (!$this->conn) {
            die("Database connection failed");
        }
    }
    
    /**
     * Lấy tất cả yêu cầu sửa chữa (cho Staff xem)
     */
    public function getAllRepairRequests($status = null) {
        $sql = "SELECT rr.*, 
                r.building, r.floor,
                CONCAT('Tòa ', r.building, ' - Tầng ', r.floor) as room_name,
                u.full_name as student_name, u.phone as student_phone,
                s.full_name as staff_name
                FROM RepairRequest rr
                JOIN Room r ON rr.room_id = r.room_id
                JOIN Student st ON rr.student_id = st.user_id
                JOIN Users u ON st.user_id = u.user_id
                LEFT JOIN Staff stf ON rr.received_by = stf.staff_id
                LEFT JOIN Users s ON stf.staff_id = s.user_id";
        
        if ($status) {
            $sql .= " WHERE rr.status = ?";
        }
        
        $sql .= " ORDER BY 
                  CASE rr.priority WHEN 'Khẩn cấp' THEN 1 ELSE 2 END,
                  rr.report_date DESC";
        
        if ($status) {
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $status);
        } else {
            $stmt = $this->conn->prepare($sql);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $requests = [];
        while ($row = $result->fetch_assoc()) {
            $requests[] = $row;
        }
        
        return [
            'success' => true,
            'requests' => $requests
        ];
    }
    
    /**
     * Lấy chi tiết yêu cầu sửa chữa
     */
    public function getRepairRequestDetails($repairId) {
        $sql = "SELECT rr.*, 
                r.building, r.floor,
                CONCAT('Tòa ', r.building, ' - Tầng ', r.floor) as room_name,
                u.full_name as student_name, u.phone as student_phone, u.email as student_email,
                s.full_name as staff_name
                FROM RepairRequest rr
                JOIN Room r ON rr.room_id = r.room_id
                JOIN Student st ON rr.student_id = st.user_id
                JOIN Users u ON st.user_id = u.user_id
                LEFT JOIN Staff stf ON rr.received_by = stf.staff_id
                LEFT JOIN Users s ON stf.staff_id = s.user_id
                WHERE rr.repair_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $repairId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return [
                'success' => true,
                'request' => $row
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Không tìm thấy yêu cầu sửa chữa'
            ];
        }
    }
    
    /**
     * Staff tiếp nhận yêu cầu sửa chữa (Mở rộng)
     */
    public function acceptRepairRequest($repairId, $staffId, $data = []) {
        // Lấy dữ liệu từ mảng
        $priority = isset($data['priority']) ? $data['priority'] : null;
        $staffNotes = isset($data['staff_notes']) ? $data['staff_notes'] : null;
        $assignedTo = isset($data['assigned_to']) ? $data['assigned_to'] : null;
        $estimatedCost = isset($data['estimated_cost']) ? $data['estimated_cost'] : null;
        $repairReport = isset($data['repair_report']) ? $data['repair_report'] : null;
        
        // Kiểm tra staff_id có tồn tại không
        $checkStaff = $this->conn->prepare("SELECT staff_id FROM Staff WHERE staff_id = ?");
        $checkStaff->bind_param("i", $staffId);
        $checkStaff->execute();
        $staffResult = $checkStaff->get_result();
        
        if ($staffResult->num_rows === 0) {
            // Staff không tồn tại, thử lấy staff đầu tiên từ database
            $getFirstStaff = $this->conn->query("SELECT staff_id FROM Staff LIMIT 1");
            if ($getFirstStaff && $row = $getFirstStaff->fetch_assoc()) {
                $staffId = $row['staff_id'];
            } else {
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy nhân viên trong hệ thống. Vui lòng đăng nhập lại.'
                ];
            }
        }
        
        // Kiểm tra xem các cột mở rộng có tồn tại không
        $hasExtendedColumns = $this->checkExtendedColumns();
        
        // Xây dựng SQL động - chỉ sử dụng các cột có sẵn trong bảng gốc
        $sql = "UPDATE RepairRequest 
                SET status = 'Đang sửa', 
                    received_by = ?";
        
        $params = [$staffId];
        $types = "i";
        
        // Thêm các trường tùy chọn có trong bảng gốc
        if ($priority !== null) {
            $sql .= ", priority = ?";
            $params[] = $priority;
            $types .= "s";
        }
        
        if ($estimatedCost !== null) {
            $sql .= ", estimated_cost = ?";
            $params[] = $estimatedCost;
            $types .= "d";
        }
        
        // Chỉ thêm các cột mở rộng nếu chúng tồn tại trong database
        if ($hasExtendedColumns) {
            $sql .= ", received_date = NOW()";
            
            if ($staffNotes !== null) {
                $sql .= ", staff_notes = ?";
                $params[] = $staffNotes;
                $types .= "s";
            }
            
            if ($assignedTo !== null) {
                $sql .= ", assigned_to = ?";
                $params[] = $assignedTo;
                $types .= "s";
            }
            
            if ($repairReport !== null) {
                $sql .= ", repair_report = ?";
                $params[] = $repairReport;
                $types .= "s";
            }
        }
        
        $sql .= " WHERE repair_id = ? AND status = 'Chờ xử lý'";
        $params[] = $repairId;
        $types .= "i";
        
        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt) {
            return [
                'success' => false,
                'message' => 'Lỗi SQL: ' . $this->conn->error
            ];
        }
        
        $stmt->bind_param($types, ...$params);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                return [
                    'success' => true,
                    'message' => 'Đã tiếp nhận yêu cầu sửa chữa thành công'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Không thể tiếp nhận yêu cầu này (có thể đã được xử lý)'
                ];
            }
        } else {
            return [
                'success' => false,
                'message' => 'Lỗi khi tiếp nhận yêu cầu: ' . $stmt->error
            ];
        }
    }
    
    /**
     * Kiểm tra xem các cột mở rộng có tồn tại trong bảng RepairRequest không
     */
    private function checkExtendedColumns() {
        $sql = "SHOW COLUMNS FROM RepairRequest LIKE 'received_date'";
        $result = $this->conn->query($sql);
        return $result && $result->num_rows > 0;
    }
    
    /**
     * Hoàn thành yêu cầu sửa chữa
     */
    public function completeRepairRequest($repairId, $actualCost = null) {
        $sql = "UPDATE RepairRequest 
                SET status = 'Hoàn thành',
                    actual_cost = ?
                WHERE repair_id = ? AND status = 'Đang sửa'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("di", $actualCost, $repairId);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                return [
                    'success' => true,
                    'message' => 'Đã hoàn thành yêu cầu sửa chữa'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Không thể hoàn thành yêu cầu này'
                ];
            }
        } else {
            return [
                'success' => false,
                'message' => 'Lỗi khi hoàn thành yêu cầu: ' . $stmt->error
            ];
        }
    }
    
    /**
     * Từ chối yêu cầu sửa chữa
     */
    public function rejectRepairRequest($repairId) {
        $sql = "UPDATE RepairRequest 
                SET status = 'Từ chối'
                WHERE repair_id = ? AND status = 'Chờ xử lý'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $repairId);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                return [
                    'success' => true,
                    'message' => 'Đã từ chối yêu cầu sửa chữa'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Không thể từ chối yêu cầu này'
                ];
            }
        } else {
            return [
                'success' => false,
                'message' => 'Lỗi khi từ chối yêu cầu: ' . $stmt->error
            ];
        }
    }
    
    /**
     * Lấy thống kê yêu cầu sửa chữa
     */
    public function getRepairStatistics() {
        $sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'Chờ xử lý' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'Đang sửa' THEN 1 ELSE 0 END) as in_progress,
                SUM(CASE WHEN status = 'Hoàn thành' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = 'Từ chối' THEN 1 ELSE 0 END) as rejected,
                SUM(CASE WHEN priority = 'Khẩn cấp' AND status IN ('Chờ xử lý', 'Đang sửa') THEN 1 ELSE 0 END) as urgent
                FROM RepairRequest";
        
        $result = $this->conn->query($sql);
        
        if ($row = $result->fetch_assoc()) {
            return [
                'success' => true,
                'stats' => $row
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Không thể lấy thống kê'
            ];
        }
    }

    /**
     * Lấy danh sách nhân viên (staff) để chỉ định
     */
    public function getStaffList() {
        $sql = "SELECT 
                    s.staff_id,
                    u.full_name,
                    u.email,
                    u.phone,
                    s.position
                FROM Staff s
                INNER JOIN Users u ON s.staff_id = u.user_id
                ORDER BY u.full_name";

        $result = $this->conn->query($sql);

        if (!$result) {
            return [
                'success' => false,
                'message' => 'Không thể lấy danh sách nhân viên: ' . $this->conn->error
            ];
        }

        $staff = [];
        while ($row = $result->fetch_assoc()) {
            $staff[] = $row;
        }

        return [
            'success' => true,
            'staff' => $staff
        ];
    }
}
?>
