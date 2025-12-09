<?php
include_once __DIR__ . '/../database.php';

class UserDetailModel {
    private $conn;
    
    function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    /**
     * Lấy thông tin chi tiết người dùng (sinh viên)
     */
    public function getUserDetail($userId) {
        $sql = "SELECT 
                    u.user_id,
                    u.username,
                    u.full_name,
                    u.email,
                    u.phone,
                    u.address,
                    u.date_of_birth,
                    u.role,
                    u.created_at,
                    s.student_id as student_code,
                    s.faculty,
                    s.major,
                    s.year_of_study,
                    s.gender,
                    s.emergency_contact,
                    s.parent_phone,
                    rr.registration_id,
                    rr.room_id,
                    rr.start_date,
                    rr.end_date,
                    rr.status as registration_status,
                    rr.request_date,
                    r.building,
                    r.floor,
                    r.capacity,
                    r.price_per_month,
                    r.gender_restriction,
                    b.bed_id,
                    b.bed_number,
                    b.status as bed_status
                FROM Users u
                LEFT JOIN Student s ON u.user_id = s.user_id
                LEFT JOIN RoomRegistration rr ON s.student_id = rr.student_id 
                    AND rr.status = 'Đã duyệt'
                LEFT JOIN Room r ON rr.room_id = r.room_id
                LEFT JOIN Bed b ON rr.bed_id = b.bed_id
                WHERE u.user_id = '$userId'";
        
        $result = mysqli_query($this->conn, $sql);
        
        if (!$result) {
            return [
                'success' => false,
                'message' => 'Lỗi truy vấn: ' . mysqli_error($this->conn)
            ];
        }
        
        $user = mysqli_fetch_assoc($result);
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Không tìm thấy người dùng'
            ];
        }
        
        return [
            'success' => true,
            'data' => $user
        ];
    }
    
    /**
     * Lấy lịch sử đăng ký phòng của người dùng
     */
    public function getUserRegistrationHistory($userId) {
        $sql = "SELECT 
                    rr.registration_id,
                    rr.room_id,
                    rr.start_date,
                    rr.end_date,
                    rr.status,
                    rr.request_date,
                    r.building,
                    r.floor,
                    b.bed_number,
                    staff.full_name as approved_by_name
                FROM RoomRegistration rr
                INNER JOIN Student s ON rr.student_id = s.student_id
                INNER JOIN Room r ON rr.room_id = r.room_id
                LEFT JOIN Bed b ON rr.bed_id = b.bed_id
                LEFT JOIN Staff st ON rr.approved_by = st.staff_id
                LEFT JOIN Users staff ON st.user_id = staff.user_id
                WHERE s.user_id = '$userId'
                ORDER BY rr.request_date DESC";
        
        $result = mysqli_query($this->conn, $sql);
        
        $history = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $history[] = $row;
            }
        }
        
        return [
            'success' => true,
            'data' => $history
        ];
    }
}
