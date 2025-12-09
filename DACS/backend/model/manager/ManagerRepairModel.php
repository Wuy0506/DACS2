<?php
/**
 * Manager Repair Model
 * Xử lý các chức năng phê duyệt yêu cầu sửa chữa dành cho Manager
 */

require_once __DIR__ . '/../database.php';

class ManagerRepairModel {
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
        
        if (!$this->conn) {
            die("Database connection failed");
        }
    }
    
    /**
     * Lấy danh sách yêu cầu sửa chữa cần phê duyệt
     */
    public function getAllRepairRequests($status = null) {
        $sql = "SELECT rr.*, 
                r.building, r.floor,
                CONCAT('Tòa ', r.building, ' - Tầng ', r.floor) as room_name,
                u.full_name as student_name, u.phone as student_phone,
                s.full_name as staff_name,
                m.full_name as manager_name
                FROM RepairRequest rr
                JOIN Room r ON rr.room_id = r.room_id
                JOIN Student st ON rr.student_id = st.user_id
                JOIN Users u ON st.user_id = u.user_id
                LEFT JOIN Staff stf ON rr.received_by = stf.staff_id
                LEFT JOIN Users s ON stf.staff_id = s.user_id
                LEFT JOIN Manager mg ON rr.approved_by = mg.manager_id
                LEFT JOIN Users m ON mg.manager_id = m.user_id";
        
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
                r.building, r.floor, r.room_id,
                CONCAT('Tòa ', r.building, ' - Tầng ', r.floor) as room_name,
                u.full_name as student_name, u.phone as student_phone, u.email as student_email,
                s.full_name as staff_name, s.user_id as staff_id,
                m.full_name as manager_name
                FROM RepairRequest rr
                JOIN Room r ON rr.room_id = r.room_id
                JOIN Student st ON rr.student_id = st.user_id
                JOIN Users u ON st.user_id = u.user_id
                LEFT JOIN Staff stf ON rr.received_by = stf.staff_id
                LEFT JOIN Users s ON stf.staff_id = s.user_id
                LEFT JOIN Manager mg ON rr.approved_by = mg.manager_id
                LEFT JOIN Users m ON mg.manager_id = m.user_id
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
     * Phê duyệt yêu cầu sửa chữa
     */
    public function approveRepairRequest($repairId, $managerId, $data = []) {
        // Kiểm tra manager_id có tồn tại không
        $checkManager = $this->conn->prepare("SELECT manager_id FROM Manager WHERE manager_id = ?");
        $checkManager->bind_param("i", $managerId);
        $checkManager->execute();
        $managerResult = $checkManager->get_result();
        
        if ($managerResult->num_rows === 0) {
            // Manager không tồn tại, thử lấy manager đầu tiên từ database
            $getFirstManager = $this->conn->query("SELECT manager_id FROM Manager LIMIT 1");
            if ($getFirstManager && $row = $getFirstManager->fetch_assoc()) {
                $managerId = $row['manager_id'];
            } else {
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy quản lý trong hệ thống. Vui lòng đăng nhập lại.'
                ];
            }
        }
        
        // Kiểm tra xem có cột approval_status không
        $hasApprovalStatus = $this->checkApprovalStatusColumn();
        
        $sql = "UPDATE RepairRequest 
                SET approved_by = ?";
        
        // Nếu có cột approval_status thì update nó, không thay đổi status chính
        if ($hasApprovalStatus) {
            $sql .= ", approval_status = 'Đã phê duyệt'";
        }
        
        $params = [$managerId];
        $types = "i";
        
        // Cập nhật estimated_cost nếu manager điều chỉnh
        if (isset($data['estimated_cost']) && !empty($data['estimated_cost'])) {
            $sql .= ", estimated_cost = ?";
            $params[] = floatval($data['estimated_cost']);
            $types .= "d";
        }
        
        // Cập nhật description nếu manager điều chỉnh
        if (isset($data['description']) && !empty($data['description'])) {
            $sql .= ", description = ?";
            $params[] = $data['description'];
            $types .= "s";
        }
        
        $sql .= " WHERE repair_id = ? AND status = 'Đang sửa'";
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
                    'message' => 'Đã phê duyệt yêu cầu sửa chữa'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Không thể phê duyệt yêu cầu này (có thể đã được xử lý hoặc chưa được staff tiếp nhận)'
                ];
            }
        } else {
            return [
                'success' => false,
                'message' => 'Lỗi khi phê duyệt yêu cầu: ' . $stmt->error
            ];
        }
    }
    
    /**
     * Từ chối yêu cầu sửa chữa
     */
    public function rejectRepairRequest($repairId, $managerId, $reason = '') {
        // Kiểm tra manager_id có tồn tại không
        $checkManager = $this->conn->prepare("SELECT manager_id FROM Manager WHERE manager_id = ?");
        $checkManager->bind_param("i", $managerId);
        $checkManager->execute();
        $managerResult = $checkManager->get_result();
        
        if ($managerResult->num_rows === 0) {
            // Manager không tồn tại, thử lấy manager đầu tiên từ database
            $getFirstManager = $this->conn->query("SELECT manager_id FROM Manager LIMIT 1");
            if ($getFirstManager && $row = $getFirstManager->fetch_assoc()) {
                $managerId = $row['manager_id'];
            } else {
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy quản lý trong hệ thống.'
                ];
            }
        }
        
        // Kiểm tra xem có cột approval_status không
        $hasApprovalStatus = $this->checkApprovalStatusColumn();
        
        $sql = "UPDATE RepairRequest 
                SET approved_by = ?,
                    status = 'Từ chối'";
        
        if ($hasApprovalStatus) {
            $sql .= ", approval_status = 'Từ chối phê duyệt'";
        }
        
        if (!empty($reason)) {
            $sql .= ", description = CONCAT(IFNULL(description, ''), '\n[Manager từ chối: " . $this->conn->real_escape_string($reason) . "]')";
        }
        
        $sql .= " WHERE repair_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $managerId, $repairId);
        
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
     * Yêu cầu điều chỉnh (Request changes)
     */
    public function requestChanges($repairId, $managerId, $changes) {
        $sql = "UPDATE RepairRequest 
                SET status = 'Chờ xử lý',
                    description = CONCAT(description, '\n[Manager yêu cầu điều chỉnh: ', ?, ']')
                WHERE repair_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $changes, $repairId);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                return [
                    'success' => true,
                    'message' => 'Đã yêu cầu điều chỉnh'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Không thể yêu cầu điều chỉnh'
                ];
            }
        } else {
            return [
                'success' => false,
                'message' => 'Lỗi khi yêu cầu điều chỉnh: ' . $stmt->error
            ];
        }
    }
    
    /**
     * Lấy thống kê yêu cầu sửa chữa cho manager
     */
    public function getRepairStatistics() {
        $sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'Chờ xử lý' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'Đang sửa' AND approved_by IS NULL THEN 1 ELSE 0 END) as waiting_approval,
                SUM(CASE WHEN status = 'Đang sửa' AND approved_by IS NOT NULL THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN status = 'Hoàn thành' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = 'Từ chối' THEN 1 ELSE 0 END) as rejected,
                SUM(CASE WHEN priority = 'Khẩn cấp' AND status IN ('Chờ xử lý', 'Đang sửa') THEN 1 ELSE 0 END) as urgent,
                IFNULL(SUM(estimated_cost), 0) as total_estimated_cost,
                IFNULL(SUM(actual_cost), 0) as total_actual_cost
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
     * Lấy danh sách yêu cầu sửa chữa cần manager phê duyệt
     * (Status = 'Đang sửa' và chưa có approved_by)
     */
    public function getPendingApprovals() {
        $sql = "SELECT rr.repair_id, rr.description, rr.priority, rr.estimated_cost, rr.report_date,
                r.building, r.floor,
                u.full_name as student_name,
                s.full_name as staff_name
                FROM RepairRequest rr
                JOIN Room r ON rr.room_id = r.room_id
                JOIN Student st ON rr.student_id = st.user_id
                JOIN Users u ON st.user_id = u.user_id
                LEFT JOIN Staff stf ON rr.received_by = stf.staff_id
                LEFT JOIN Users s ON stf.staff_id = s.user_id
                WHERE rr.status = 'Đang sửa' AND rr.approved_by IS NULL
                ORDER BY 
                  CASE rr.priority WHEN 'Khẩn cấp' THEN 1 ELSE 2 END,
                  rr.report_date DESC
                LIMIT 10";
        
        $result = $this->conn->query($sql);
        
        $repairs = [];
        while ($row = $result->fetch_assoc()) {
            $repairs[] = $row;
        }
        
        return [
            'success' => true,
            'repairs' => $repairs
        ];
    }
    
    /**
     * Kiểm tra xem cột approval_status có tồn tại không
     */
    private function checkApprovalStatusColumn() {
        $sql = "SHOW COLUMNS FROM RepairRequest LIKE 'approval_status'";
        $result = $this->conn->query($sql);
        return $result && $result->num_rows > 0;
    }
}
?>
