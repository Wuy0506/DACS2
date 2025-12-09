<?php
include_once __DIR__ . '/../database.php';

/**
 * Model xử lý đăng ký phòng của khách hàng (Customer)
 */
class CustomerRegistrationModel {
    private $conn;
    
    function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    /**
     * Lấy danh sách đăng ký phòng của một sinh viên
     */
    public function getRegistrationsByUserId($userId) {
        // Escape user input để tránh SQL injection
        $userId = mysqli_real_escape_string($this->conn, $userId);
        
        $sql = "SELECT 
                    rr.registration_id,
                    rr.student_id,
                    rr.room_id,
                    rr.bed_id,
                    rr.start_date,
                    rr.end_date,
                    rr.status,
                    rr.request_date,
                    rr.approved_by,
                    u.user_id,
                    u.full_name,
                    u.email,
                    u.phone,
                    s.faculty,
                    s.major,
                    s.student_id as student_code,
                    r.building,
                    r.floor,
                    r.capacity,
                    r.price_per_month,
                    r.gender_restriction,
                    b.bed_number,
                    b.status as bed_status,
                    c.contract_id,
                    c.created_date as contract_date,
                    c.end_date as contract_end_date,
                    c.status as contract_status,
                    approver.full_name as approver_name
                FROM RoomRegistration rr
                INNER JOIN Student s ON rr.student_id = s.user_id
                INNER JOIN Users u ON s.user_id = u.user_id
                LEFT JOIN Room r ON rr.room_id = r.room_id
                LEFT JOIN Bed b ON rr.bed_id = b.bed_id
                LEFT JOIN Contract c ON rr.registration_id = c.registration_id
                LEFT JOIN Staff st ON rr.approved_by = st.staff_id
                LEFT JOIN Users approver ON st.staff_id = approver.user_id
                WHERE s.user_id = ?
                ORDER BY rr.request_date DESC";
        
        // Sử dụng prepared statement
        $stmt = mysqli_prepare($this->conn, $sql);
        
        if (!$stmt) {
            error_log("Prepare failed: " . mysqli_error($this->conn));
            return [
                'success' => false,
                'message' => 'Lỗi chuẩn bị truy vấn: ' . mysqli_error($this->conn)
            ];
        }
        
        mysqli_stmt_bind_param($stmt, "i", $userId);
        
        if (!mysqli_stmt_execute($stmt)) {
            error_log("Execute failed: " . mysqli_stmt_error($stmt));
            return [
                'success' => false,
                'message' => 'Lỗi thực thi truy vấn: ' . mysqli_stmt_error($stmt)
            ];
        }
        
        $result = mysqli_stmt_get_result($stmt);
        
        if (!$result) {
            error_log("Get result failed: " . mysqli_error($this->conn));
            return [
                'success' => false,
                'message' => 'Lỗi lấy kết quả: ' . mysqli_error($this->conn)
            ];
        }
        
        $registrations = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $registrations[] = $row;
        }
        
        mysqli_stmt_close($stmt);
        
        return [
            'success' => true,
            'data' => $registrations
        ];
    }
    
    /**
     * Lấy chi tiết một đăng ký cụ thể
     */
    public function getRegistrationDetail($registrationId, $userId) {
        $sql = "SELECT 
                    rr.registration_id,
                    rr.student_id,
                    rr.room_id,
                    rr.bed_id,
                    rr.start_date,
                    rr.end_date,
                    rr.status,
                    rr.request_date,
                    rr.approved_by,
                    u.user_id,
                    u.full_name,
                    u.email,
                    u.phone,
                    s.faculty,
                    s.major,
                    s.gender,
                    s.student_id as student_code,
                    s.address,
                    r.building,
                    r.floor,
                    r.capacity,
                    r.available_beds,
                    r.price_per_month,
                    r.gender_restriction,
                    b.bed_number,
                    b.status as bed_status,
                    c.contract_id,
                    c.created_date as contract_date,
                    c.end_date as contract_end_date,
                    c.status as contract_status,
                    approver.full_name as approver_name
                FROM RoomRegistration rr
                INNER JOIN Student s ON rr.student_id = s.user_id
                INNER JOIN Users u ON s.user_id = u.user_id
                LEFT JOIN Room r ON rr.room_id = r.room_id
                LEFT JOIN Bed b ON rr.bed_id = b.bed_id
                LEFT JOIN Contract c ON rr.registration_id = c.registration_id
                LEFT JOIN Staff st ON rr.approved_by = st.staff_id
                LEFT JOIN Users approver ON st.staff_id = approver.user_id
                WHERE rr.registration_id = '$registrationId' 
                AND s.user_id = '$userId'";
        
        $result = mysqli_query($this->conn, $sql);
        
        if (!$result) {
            return [
                'success' => false,
                'message' => 'Lỗi truy vấn: ' . mysqli_error($this->conn)
            ];
        }
        
        $data = mysqli_fetch_assoc($result);
        
        if (!$data) {
            return [
                'success' => false,
                'message' => 'Không tìm thấy đăng ký hoặc bạn không có quyền xem'
            ];
        }
        
        return [
            'success' => true,
            'data' => $data
        ];
    }

    public function huyDangKyPhong($registrationId,$userId){
        //kiểm tra xem đơn đó tồn tại không
        $sqlCheck = "SELECT registration_id, status from RoomRegistration where registration_id = ? and student_id = ?";
        $stmt = mysqli_prepare($this->conn,$sqlCheck);
        mysqli_stmt_bind_param($stmt, "ii", $registrationId, $userId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $reg_id, $status);

        if(!mysqli_stmt_fetch($stmt)){
            return [
                'success'=>false,
                'message'=>'Không tìm thấy đơn hoặc không có quyền'
            ];
        }
        mysqli_stmt_close($stmt);
            $reg = [
                'registration_id' => $reg_id,
                'status' => $status
            ];

        //chỉ cho xoá khi trạng thái chờ duyệt
        if($reg['status'] !== 'Chờ duyệt'){
            return [
            'success' => false,
            'message' => 'Chỉ có thể hủy đơn ở trạng thái Chờ duyệt'
            ];
        }

        //xoá
        $sqlDelete = "DELETE FROM RoomRegistration WHERE registration_id = ? AND student_id = ?";

        $stmt2 = mysqli_prepare($this->conn, $sqlDelete);
        mysqli_stmt_bind_param($stmt2, "ii", $registrationId, $userId);

        if (!mysqli_stmt_execute($stmt2)) {
            return [
                'success' => false,
                'message' => 'Lỗi khi hủy đăng ký: ' . mysqli_stmt_error($stmt2)
            ];
        }

        return [
            'success' => true,
            'message' => 'Hủy đăng ký phòng thành công'
        ];
    }

    
    /**
     * Lấy thống kê đăng ký của user
     */
    public function getStatisticsByUserId($userId) {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'Chờ duyệt' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'Đã duyệt' THEN 1 ELSE 0 END) as approved,
                    SUM(CASE WHEN status = 'Từ chối' THEN 1 ELSE 0 END) as rejected
                FROM RoomRegistration
                WHERE student_id = '$userId'";
        
        $result = mysqli_query($this->conn, $sql);
        
        if ($result) {
            $stats = mysqli_fetch_assoc($result);
            return [
                'success' => true,
                'data' => $stats
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Lỗi truy vấn: ' . mysqli_error($this->conn)
        ];
    }
    
    /**
     * Lấy thông tin thanh toán của user
     */
    public function getPaymentsByUserId($userId) {
        $sql = "SELECT 
                    p.payment_id,
                    p.payment_type,
                    p.amount,
                    p.payment_date,
                    p.payment_method,
                    p.description,
                    c.contract_id,
                    c.created_date as contract_start,
                    c.end_date as contract_end,
                    rr.registration_id,
                    r.building,
                    r.floor,
                    b.bed_number
                FROM Payment p
                INNER JOIN Contract c ON p.contract_id = c.contract_id
                INNER JOIN RoomRegistration rr ON c.registration_id = rr.registration_id
                INNER JOIN Room r ON rr.room_id = r.room_id
                LEFT JOIN Bed b ON rr.bed_id = b.bed_id
                WHERE p.student_id = '$userId'
                ORDER BY p.payment_date DESC";
        
        $result = mysqli_query($this->conn, $sql);
        
        if (!$result) {
            return [
                'success' => false,
                'message' => 'Lỗi truy vấn: ' . mysqli_error($this->conn)
            ];
        }
        
        $payments = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $payments[] = $row;
        }
        
        return [
            'success' => true,
            'data' => $payments
        ];
    }

    
}
