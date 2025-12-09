<?php
include_once __DIR__ . '/../database.php';
class BookingModel {
    private $conn;
    
    function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
 
    /**
     * Lấy thông tin người dùng để điền vào form
     */
    public function laythongtinnguoidung($user_id) {
        // Chỉ lấy thông tin từ User, không cần Student
        // Nếu cần thông tin Student thì LEFT JOIN sẽ trả về NULL cho các trường đó
        $sql = "SELECT 
                    u.user_id,
                    u.full_name,
                    u.email,
                    u.phone,
                    u.role,
                    s.faculty,
                    s.major,
                    s.gender,
                    s.address
                FROM Users u
                LEFT JOIN Student s ON u.user_id = s.user_id
                WHERE u.user_id = '$user_id'";
        
        error_log("=== DEBUG laythongtinnguoidung ===");
        error_log("SQL: " . $sql);
        
        $result = mysqli_query($this->conn, $sql);
        
        if (!$result) {
            error_log("Query ERROR: " . mysqli_error($this->conn));
            return [
                'success' => false,
                'message' => 'Lỗi truy vấn: ' . mysqli_error($this->conn)
            ];
        }
        
        $data = mysqli_fetch_assoc($result);
        error_log("Query result: " . print_r($data, true));
        
        if (!$data) {
            error_log("No data found for user_id: " . $user_id);
            return [
                'success' => false,
                'message' => 'Không tìm thấy thông tin người dùng với user_id = ' . $user_id
            ];
        }
        
        // Trả về dữ liệu (có thể có các trường NULL nếu chưa có thông tin Student)
        return [
            'success' => true,
            'data' => [
                'user_id' => $data['user_id'],
                'full_name' => $data['full_name'] ?? '',
                'email' => $data['email'] ?? '',
                'phone' => $data['phone'] ?? '',
                'faculty' => $data['faculty'] ?? '',
                'major' => $data['major'] ?? '',
                'gender' => $data['gender'] ?? '',
                'address' => $data['address'] ?? ''
            ]
        ];
    }
        
    public function getRoomDetails($roomId) {
        $sql = "SELECT 
                    r.room_id,
                    r.building,
                    r.floor,
                    r.capacity,
                    r.available_beds,
                    r.gender_restriction,
                    r.price_per_month,
                    r.status
                FROM Room r
                WHERE r.room_id = '$roomId'";
        
        $result = mysqli_query($this->conn, $sql);
        
        if (!$result) {
            return [
                'success' => false,
                'message' => 'Lỗi khi lấy thông tin phòng: ' . mysqli_error($this->conn)
            ];
        }
        
        $room = mysqli_fetch_assoc($result);
        
        if (!$room) {
            return [
                'success' => false,
                'message' => 'Không tìm thấy phòng'
            ];
        }
        
        // Lấy danh sách giường trống
        $beds = $this->getAvailableBeds($roomId);
        
        return [
            'success' => true,
            'data' => [
                'room' => $room,
                'beds' => $beds
            ]
        ];
    }
    
    /**
     * Lấy số lượng giường trống trong phòng
     * Loại bỏ các giường đang có người chờ duyệt hoặc đã duyệt
     */
    public function getAvailableBeds($roomId) {
        $sql = "SELECT 
                    b.bed_id,
                    b.bed_number,
                    b.status
                FROM Bed b
                WHERE b.room_id = '$roomId' 
                AND b.status = 'Trống'
                AND b.bed_id NOT IN (
                    SELECT bed_id 
                    FROM RoomRegistration 
                    WHERE bed_id IS NOT NULL 
                    AND status IN ('Chờ duyệt', 'Đã duyệt')
                )
                ORDER BY b.bed_number ASC";
        
        $result = mysqli_query($this->conn, $sql);
        
        $beds = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $beds[] = $row;
            }
        }
        
        return $beds;
    }
    
    /**
     * Tạo đăng ký phòng mới (có chọn giường)
     */
    public function createRoomRegistration($studentId, $roomId, $bedId, $startDate, $endDate, $studentInfo = null) {
        // Cập nhật thông tin sinh viên nếu có student_id
        if (!empty($studentId) && $studentInfo && isset($studentInfo['full_name'])) {
            $this->updateStudentInfo($studentId, $studentInfo);
        }
        
        // Kiểm tra sinh viên đã có đăng ký chưa duyệt hoặc đang hiệu lực (chỉ khi có student_id)
        if (!empty($studentId)) {
            $checkSql = "SELECT COUNT(*) as count 
                         FROM RoomRegistration 
                         WHERE student_id = '$studentId' 
                         AND status IN ('Chờ duyệt', 'Đã duyệt')";
            
            $checkResult = mysqli_query($this->conn, $checkSql);
            $checkRow = mysqli_fetch_assoc($checkResult);
            
            if ($checkRow['count'] > 0) {
                return [
                    'success' => false,
                    'message' => 'Bạn đã có đăng ký phòng đang chờ duyệt hoặc đã có phòng'
                ];
            }
        }
        
        // Kiểm tra giường có tồn tại và đang trống không
        if (!empty($bedId)) {
            $bedCheckSql = "SELECT status, room_id FROM Bed WHERE bed_id = '$bedId'";
            $bedCheckResult = mysqli_query($this->conn, $bedCheckSql);
            $bedRow = mysqli_fetch_assoc($bedCheckResult);
            
            if (!$bedRow) {
                return [
                    'success' => false,
                    'message' => 'Giường không tồn tại'
                ];
            }
            
            if ($bedRow['room_id'] != $roomId) {
                return [
                    'success' => false,
                    'message' => 'Giường không thuộc phòng này'
                ];
            }
            
            if ($bedRow['status'] !== 'Trống') {
                return [
                    'success' => false,
                    'message' => 'Giường đã được đặt. Vui lòng chọn giường khác'
                ];
            }
        }
        
        // Kiểm tra phòng còn chỗ trống không
        $roomCheckSql = "SELECT available_beds FROM Room WHERE room_id = '$roomId'";
        $roomCheckResult = mysqli_query($this->conn, $roomCheckSql);
        $roomRow = mysqli_fetch_assoc($roomCheckResult);
        
        if (!$roomRow || $roomRow['available_beds'] <= 0) {
            return [
                'success' => false,
                'message' => 'Phòng đã hết chỗ trống. Vui lòng chọn phòng khác'
            ];
        }
        
        // Tạo đăng ký mới
        $requestDate = date('Y-m-d H:i:s');
        
        // Xử lý student_id (có thể là NULL)
        $studentIdValue = empty($studentId) ? 'NULL' : "'$studentId'";
        
        // Chuẩn bị thông tin khách (nếu không có student_id)
        $guestInfoJson = 'NULL';
        if (empty($studentId) && $studentInfo) {
            $guestInfoJson = "'" . mysqli_real_escape_string($this->conn, json_encode($studentInfo, JSON_UNESCAPED_UNICODE)) . "'";
        }
        
        // Kiểm tra xem cột guest_info có tồn tại không, nếu không thì tạo
        $this->ensureGuestInfoColumn();
        
        // Thêm bed_id vào RoomRegistration
        $bedIdValue = !empty($bedId) ? "'$bedId'" : 'NULL';
        
        $sql = "INSERT INTO RoomRegistration 
                (student_id, room_id, bed_id, start_date, end_date, status, request_date, guest_info) 
                VALUES 
                ($studentIdValue, '$roomId', $bedIdValue, '$startDate', '$endDate', 'Chờ duyệt', '$requestDate', $guestInfoJson)";
        
        $result = mysqli_query($this->conn, $sql);
        
        if (!$result) {
            return [
                'success' => false,
                'message' => 'Lỗi khi tạo đăng ký: ' . mysqli_error($this->conn)
            ];
        }
        
        $registrationId = mysqli_insert_id($this->conn);
        
        // LƯU Ý: Giường chỉ được đánh dấu "Đang sử dụng" khi đăng ký được DUYỆT
        // Không cập nhật trạng thái giường ở đây
        
        return [
            'success' => true,
            'message' => 'Đăng ký phòng thành công! Vui lòng chờ quản lý phê duyệt.',
            'registration_id' => $registrationId
        ];
    }
    
    /**
     * Cập nhật thông tin sinh viên
     */
    private function updateStudentInfo($studentId, $info) {
        // Cập nhật bảng User (chỉ cập nhật các trường có dữ liệu)
        if (isset($info['full_name']) || isset($info['email']) || isset($info['phone'])) {
            $updates = [];
            
            // Chỉ cập nhật full_name nếu có giá trị và không rỗng
            if (isset($info['full_name']) && !empty(trim($info['full_name']))) {
                $fullName = mysqli_real_escape_string($this->conn, trim($info['full_name']));
                $updates[] = "full_name = '$fullName'";
            }
            
            if (isset($info['email']) && !empty(trim($info['email']))) {
                $email = mysqli_real_escape_string($this->conn, trim($info['email']));
                $updates[] = "email = '$email'";
            }
            
            if (isset($info['phone']) && !empty(trim($info['phone']))) {
                $phone = mysqli_real_escape_string($this->conn, trim($info['phone']));
                $updates[] = "phone = '$phone'";
            }
            
            if (!empty($updates)) {
                $sql = "UPDATE Users SET " . implode(', ', $updates) . " WHERE user_id = '$studentId'";
                mysqli_query($this->conn, $sql);
            }
        }
        
        // Xử lý bảng Student: TẠO hoặc CẬP NHẬT
        if (isset($info['faculty']) && !empty(trim($info['faculty']))) {
            $faculty = mysqli_real_escape_string($this->conn, trim($info['faculty']));
            
            // Kiểm tra xem student_id đã có trong bảng Student chưa
            $checkSql = "SELECT user_id FROM Student WHERE user_id = '$studentId'";
            $checkResult = mysqli_query($this->conn, $checkSql);
            
            if (mysqli_num_rows($checkResult) > 0) {
                // Đã có → UPDATE
                $sql = "UPDATE Student SET faculty = '$faculty' WHERE user_id = '$studentId'";
                mysqli_query($this->conn, $sql);
            } else {
                // Chưa có → INSERT (tạo mới record với faculty, các trường khác để NULL)
                // Theo quy.sql: Student có cột student_id (VARCHAR), faculty, major, gender, date_of_birth, address
                $sql = "INSERT INTO Student (user_id, student_id, faculty, major, gender, date_of_birth, address) 
                        VALUES ('$studentId', NULL, '$faculty', NULL, NULL, NULL, NULL)";
                mysqli_query($this->conn, $sql);
            }
        }
    }
    public function checkGenderRestriction($studentId, $roomId) {
        // Lấy thông tin phòng
        $roomSql = "SELECT gender_restriction FROM Room WHERE room_id = '$roomId'";
        $roomResult = mysqli_query($this->conn, $roomSql);
        $room = mysqli_fetch_assoc($roomResult);
        
        if (!$room) {
            return [
                'success' => false,
                'message' => 'Không tìm thấy thông tin phòng'
            ];
        }
        
        // Nếu phòng không giới hạn giới tính (NULL) → Bỏ qua kiểm tra
        if (empty($room['gender_restriction']) || $room['gender_restriction'] === null) {
            return ['success' => true];
        }
        
        // Kiểm tra xem student có trong bảng Student không
        $studentSql = "SELECT gender FROM Student WHERE user_id = '$studentId'";
        $studentResult = mysqli_query($this->conn, $studentSql);
        $student = mysqli_fetch_assoc($studentResult);
        
        // Nếu student chưa có thông tin trong bảng Student → Bỏ qua kiểm tra (admin sẽ kiểm tra sau)
        if (!$student || empty($student['gender'])) {
            return [
                'success' => true,
                'note' => 'Chưa có thông tin giới tính - Admin sẽ kiểm tra khi duyệt'
            ];
        }
        
        // Kiểm tra giới tính khớp
        if ($student['gender'] == $room['gender_restriction']) {
            return ['success' => true];
        }
        
        return [
            'success' => false,
            'message' => 'Giới tính của bạn không phù hợp với yêu cầu của phòng này'
        ];
    }
    
    /**
     * Đảm bảo cột guest_info tồn tại trong bảng RoomRegistration
     */
    private function ensureGuestInfoColumn() {
        // Kiểm tra xem cột đã tồn tại chưa
        $checkSql = "SHOW COLUMNS FROM RoomRegistration LIKE 'guest_info'";
        $result = mysqli_query($this->conn, $checkSql);
        
        if (mysqli_num_rows($result) == 0) {
            // Cột chưa tồn tại, tạo mới
            $alterSql = "ALTER TABLE RoomRegistration ADD COLUMN guest_info TEXT NULL COMMENT 'Thông tin khách (JSON) khi chưa có student_id'";
            mysqli_query($this->conn, $alterSql);
        }
    }
}
