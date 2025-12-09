<?php
require_once __DIR__ . '/../database.php';

/**
 * Model xử lý yêu cầu sửa chữa
 */
class RepairRequestModel {
    private $conn;
    
    function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    /**
     * Lấy danh sách phòng của sinh viên
     */
    public function getStudentRooms($studentId) {
        $sql = "SELECT DISTINCT r.room_id, r.building, r.floor, 
                CONCAT('Tòa ', r.building, ' - Tầng ', r.floor) as room_name
                FROM RoomRegistration rr
                JOIN Room r ON rr.room_id = r.room_id
                WHERE rr.student_id = ? AND rr.status = 'Đã duyệt'
                AND CURDATE() BETWEEN rr.start_date AND rr.end_date";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $studentId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $rooms = [];
        while ($row = $result->fetch_assoc()) {
            $rooms[] = $row;
        }
        
        return [
            'success' => true,
            'rooms' => $rooms
        ];
    }
    
    /**
     * Tạo yêu cầu sửa chữa mới
     */
    public function createRepairRequest($studentId, $roomId, $description, $imageUrl, $priority) {
        // Kiểm tra sinh viên có đang ở phòng này không
        $checkSql = "SELECT 1 FROM RoomRegistration 
                     WHERE student_id = ? AND room_id = ? 
                     AND status = 'Đã duyệt'
                     AND CURDATE() BETWEEN start_date AND end_date";
        $checkStmt = $this->conn->prepare($checkSql);
        $checkStmt->bind_param("ii", $studentId, $roomId);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        
        if ($checkResult->num_rows == 0) {
            return [
                'success' => false,
                'message' => 'Bạn không có quyền báo cáo sửa chữa cho phòng này'
            ];
        }
        
        // Tạo yêu cầu sửa chữa
        $sql = "INSERT INTO RepairRequest (room_id, student_id, description, image_url, priority, status, report_date) 
                VALUES (?, ?, ?, ?, ?, 'Chờ xử lý', NOW())";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iisss", $roomId, $studentId, $description, $imageUrl, $priority);
        
        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'Đã gửi yêu cầu sửa chữa thành công',
                'repair_id' => $stmt->insert_id
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Lỗi khi tạo yêu cầu: ' . $stmt->error
            ];
        }
    }
    
    /**
     * Lấy danh sách yêu cầu sửa chữa của sinh viên
     */
    public function getStudentRepairRequests($studentId, $status = null) {
        $sql = "SELECT rr.*, 
                r.building, r.floor,
                CONCAT('Tòa ', r.building, ' - Tầng ', r.floor) as room_name,
                s.full_name as staff_name
                FROM RepairRequest rr
                JOIN Room r ON rr.room_id = r.room_id
                LEFT JOIN Staff st ON rr.received_by = st.staff_id
                LEFT JOIN Users s ON st.staff_id = s.user_id
                WHERE rr.student_id = ?";
        
        if ($status) {
            $sql .= " AND rr.status = ?";
        }
        
        $sql .= " ORDER BY rr.report_date DESC";
        
        $stmt = $this->conn->prepare($sql);
        
        if ($status) {
            $stmt->bind_param("is", $studentId, $status);
        } else {
            $stmt->bind_param("i", $studentId);
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
    
}
