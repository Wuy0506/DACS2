<?php
include_once __DIR__ . '/../database.php';

class RoomManagementModel {
    private $conn;
    
    function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    /**
     * Lấy danh sách tất cả phòng với thông tin cơ bản
     */
    public function getAllRooms($status = null) {
        $sql = "SELECT 
                    r.room_id,
                    r.room_name,
                    r.building,
                    r.floor,
                    r.capacity,
                    r.available_beds,
                    r.gender_restriction,
                    r.price_per_month,
                    r.status,
                    COUNT(DISTINCT rr.registration_id) as total_registrations,
                    COUNT(DISTINCT CASE WHEN b.status = 'Đang sử dụng' THEN b.bed_id END) as occupied_beds
                FROM Room r
                LEFT JOIN Bed b ON r.room_id = b.room_id
                LEFT JOIN RoomRegistration rr ON r.room_id = rr.room_id AND rr.status = 'Đã duyệt'
                WHERE 1=1";
        
        if ($status !== null && $status !== '') {
            $sql .= " AND r.status = '" . mysqli_real_escape_string($this->conn, $status) . "'";
        }
        
        $sql .= " GROUP BY r.room_id
                  ORDER BY r.building ASC, r.floor ASC";
        
        error_log("=== DEBUG getAllRooms ===");
        error_log("SQL: " . $sql);
        
        $result = mysqli_query($this->conn, $sql);
        
        if (!$result) {
            error_log("Query ERROR: " . mysqli_error($this->conn));
            return [
                'success' => false,
                'message' => 'Lỗi truy vấn: ' . mysqli_error($this->conn)
            ];
        }
        
        $rooms = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rooms[] = $row;
        }
        
        return [
            'success' => true,
            'data' => $rooms
        ];
    }
    
    /**
     * Lấy chi tiết phòng bao gồm thông tin người ở
     */
    public function getRoomDetail($roomId) {
        // Lấy thông tin phòng
        $roomSql = "SELECT 
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
        
        $roomResult = mysqli_query($this->conn, $roomSql);
        
        if (!$roomResult) {
            return [
                'success' => false,
                'message' => 'Lỗi truy vấn phòng: ' . mysqli_error($this->conn)
            ];
        }
        
        $room = mysqli_fetch_assoc($roomResult);
        
        if (!$room) {
            return [
                'success' => false,
                'message' => 'Không tìm thấy phòng'
            ];
        }
        
        // Lấy danh sách giường và người ở
        $bedsSql = "SELECT 
                        b.bed_id,
                        b.bed_number,
                        b.status as bed_status,
                        u.user_id,
                        u.full_name,
                        u.email,
                        u.phone,
                        s.faculty,
                        s.major,
                        s.gender,
                        s.student_id as student_code,
                        rr.registration_id,
                        rr.start_date,
                        rr.end_date,
                        rr.guest_info
                    FROM Bed b
                    LEFT JOIN RoomRegistration rr ON b.bed_id = rr.bed_id 
                        AND rr.status = 'Đã duyệt'
                        AND b.status = 'Đang sử dụng'
                    LEFT JOIN Users u ON rr.student_id = u.user_id
                    LEFT JOIN Student s ON u.user_id = s.user_id
                    WHERE b.room_id = '$roomId'
                    ORDER BY b.bed_number ASC";
        
        $bedsResult = mysqli_query($this->conn, $bedsSql);
        
        $beds = [];
        if ($bedsResult) {
            while ($row = mysqli_fetch_assoc($bedsResult)) {
                // Xử lý guest_info nếu có
                if (empty($row['user_id']) && !empty($row['guest_info'])) {
                    $guestInfo = json_decode($row['guest_info'], true);
                    if ($guestInfo) {
                        $row['full_name'] = $guestInfo['full_name'] ?? '';
                        $row['email'] = $guestInfo['email'] ?? '';
                        $row['phone'] = $guestInfo['phone'] ?? '';
                        $row['faculty'] = $guestInfo['faculty'] ?? '';
                        $row['is_guest'] = true;
                    }
                }
                $beds[] = $row;
            }
        }
        
        return [
            'success' => true,
            'data' => [
                'room' => $room,
                'beds' => $beds
            ]
        ];
    }
    
    /**
     * Cập nhật trạng thái phòng
     */
    public function updateRoomStatus($roomId, $status) {
        $validStatus = ['Trống', 'Đầy', 'Bảo trì'];
        
        if (!in_array($status, $validStatus)) {
            return [
                'success' => false,
                'message' => 'Trạng thái không hợp lệ'
            ];
        }
        
        $sql = "UPDATE Room 
                SET status = '" . mysqli_real_escape_string($this->conn, $status) . "'
                WHERE room_id = '$roomId'";
        
        if (mysqli_query($this->conn, $sql)) {
            return [
                'success' => true,
                'message' => 'Cập nhật trạng thái phòng thành công'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Lỗi: ' . mysqli_error($this->conn)
            ];
        }
    }
    
    /**
     * Lấy thống kê phòng
     */
    public function getRoomStatistics() {
        $sql = "SELECT 
                    COUNT(*) as total_rooms,
                    SUM(CASE WHEN status = 'Trống' THEN 1 ELSE 0 END) as empty_rooms,
                    SUM(CASE WHEN status = 'Đầy' THEN 1 ELSE 0 END) as full_rooms,
                    SUM(CASE WHEN status = 'Bảo trì' THEN 1 ELSE 0 END) as maintenance_rooms,
                    SUM(capacity) as total_capacity,
                    SUM(available_beds) as total_available
                FROM Room";
        
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
            'message' => 'Lỗi lấy thống kê'
        ];
    }
    
    /**
     * Tìm kiếm sinh viên để thêm vào phòng
     */
    public function searchStudents($keyword) {
        $keyword = mysqli_real_escape_string($this->conn, $keyword);
        
        $sql = "SELECT 
                    u.user_id,
                    u.full_name,
                    u.email,
                    u.phone,
                    s.student_id as student_code,
                    s.faculty,
                    s.major,
                    s.gender,
                    CASE 
                        WHEN EXISTS (
                            SELECT 1 FROM RoomRegistration rr 
                            WHERE rr.student_id = u.user_id 
                            AND rr.status = 'Đã duyệt'
                            AND rr.end_date >= CURDATE()
                        ) THEN 1
                        ELSE 0
                    END as has_active_room
                FROM Users u
                INNER JOIN Student s ON u.user_id = s.user_id
                WHERE u.role = 'student'
                AND (
                    u.full_name LIKE '%$keyword%' 
                    OR u.email LIKE '%$keyword%'
                    OR s.student_id LIKE '%$keyword%'
                    OR u.phone LIKE '%$keyword%'
                )
                ORDER BY u.full_name ASC
                LIMIT 20";
        
        $result = mysqli_query($this->conn, $sql);
        
        if (!$result) {
            return [
                'success' => false,
                'message' => 'Lỗi tìm kiếm: ' . mysqli_error($this->conn)
            ];
        }
        
        $students = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $students[] = $row;
        }
        
        return [
            'success' => true,
            'data' => $students
        ];
    }
    
    /**
     * Thêm sinh viên vào phòng (tạo đăng ký và phân giường)
     */
    public function assignStudentToRoom($studentId, $roomId, $startDate, $endDate, $staffId) {
        mysqli_begin_transaction($this->conn);
        
        try {
            // 1. Kiểm tra sinh viên đã có phòng chưa
            $checkSql = "SELECT registration_id 
                        FROM RoomRegistration 
                        WHERE student_id = '$studentId' 
                        AND status = 'Đã duyệt'
                        AND end_date >= CURDATE()";
            $checkResult = mysqli_query($this->conn, $checkSql);
            
            if (mysqli_num_rows($checkResult) > 0) {
                throw new Exception('Sinh viên đã có phòng đang hoạt động');
            }
            
            // 2. Kiểm tra phòng còn chỗ không
            $roomSql = "SELECT r.room_id, r.available_beds, r.gender_restriction, s.gender
                       FROM Room r
                       CROSS JOIN Student s
                       WHERE r.room_id = '$roomId' 
                       AND s.user_id = '$studentId'";
            $roomResult = mysqli_query($this->conn, $roomSql);
            $roomData = mysqli_fetch_assoc($roomResult);
            
            if (!$roomData) {
                throw new Exception('Không tìm thấy phòng hoặc sinh viên');
            }
            
            if ($roomData['available_beds'] <= 0) {
                throw new Exception('Phòng đã hết chỗ');
            }
            
            // 3. Kiểm tra giới tính
            if ($roomData['gender_restriction'] !== 'Không giới hạn' && 
                $roomData['gender_restriction'] !== $roomData['gender']) {
                throw new Exception('Giới tính sinh viên không phù hợp với phòng');
            }
            
            // 4. Tìm giường trống
            $bedSql = "SELECT bed_id 
                      FROM Bed 
                      WHERE room_id = '$roomId' 
                      AND status = 'Trống' 
                      LIMIT 1";
            $bedResult = mysqli_query($this->conn, $bedSql);
            $bedData = mysqli_fetch_assoc($bedResult);
            
            if (!$bedData) {
                throw new Exception('Không tìm thấy giường trống');
            }
            
            $bedId = $bedData['bed_id'];
            
            // 5. Tạo đăng ký phòng với bed_id
            $insertRegSql = "INSERT INTO RoomRegistration 
                           (student_id, room_id, bed_id, start_date, end_date, status, request_date, approved_by)
                           VALUES 
                           ('$studentId', '$roomId', '$bedId', '$startDate', '$endDate', 'Đã duyệt', NOW(), '$staffId')";
            
            if (!mysqli_query($this->conn, $insertRegSql)) {
                throw new Exception('Lỗi tạo đăng ký: ' . mysqli_error($this->conn));
            }
            
            $registrationId = mysqli_insert_id($this->conn);
            
            // 7. Cập nhật trạng thái giường
            $updateBedSql = "UPDATE Bed 
                           SET status = 'Đang sử dụng' 
                           WHERE bed_id = '$bedId'";
            
            if (!mysqli_query($this->conn, $updateBedSql)) {
                throw new Exception('Lỗi cập nhật trạng thái giường: ' . mysqli_error($this->conn));
            }
            
            // 8. Cập nhật available_beds của phòng
            $updateRoomSql = "UPDATE Room 
                            SET available_beds = available_beds - 1,
                                status = CASE 
                                    WHEN available_beds - 1 <= 0 THEN 'Đầy'
                                    ELSE status
                                END
                            WHERE room_id = '$roomId'";
            
            if (!mysqli_query($this->conn, $updateRoomSql)) {
                throw new Exception('Lỗi cập nhật phòng: ' . mysqli_error($this->conn));
            }
            
            // 9. Tạo hợp đồng
            $contractSql = "INSERT INTO Contract 
                          (registration_id, created_date, end_date, status)
                          VALUES 
                          ('$registrationId', CURDATE(), '$endDate', 'Hiệu lực')";
            
            if (!mysqli_query($this->conn, $contractSql)) {
                throw new Exception('Lỗi tạo hợp đồng: ' . mysqli_error($this->conn));
            }
            
            mysqli_commit($this->conn);
            
            return [
                'success' => true,
                'message' => 'Thêm sinh viên vào phòng thành công',
                'data' => [
                    'registration_id' => $registrationId,
                    'bed_id' => $bedId
                ]
            ];
            
        } catch (Exception $e) {
            mysqli_rollback($this->conn);
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Xóa sinh viên khỏi phòng (hủy đăng ký)
     */
    public function removeStudentFromRoom($registrationId) {
        mysqli_begin_transaction($this->conn);
        
        try {
            // 1. Lấy thông tin đăng ký
            $regSql = "SELECT rr.room_id, rr.bed_id, rr.student_id
                      FROM RoomRegistration rr
                      WHERE rr.registration_id = '$registrationId'
                      AND rr.status = 'Đã duyệt'";
            $regResult = mysqli_query($this->conn, $regSql);
            $regData = mysqli_fetch_assoc($regResult);
            
            if (!$regData) {
                throw new Exception('Không tìm thấy đăng ký');
            }
            
            $roomId = $regData['room_id'];
            $bedId = $regData['bed_id'];
            
            // 2. Cập nhật trạng thái đăng ký
            $updateRegSql = "UPDATE RoomRegistration 
                           SET status = 'Từ chối',
                               end_date = CURDATE()
                           WHERE registration_id = '$registrationId'";
            
            if (!mysqli_query($this->conn, $updateRegSql)) {
                throw new Exception('Lỗi cập nhật đăng ký: ' . mysqli_error($this->conn));
            }
            
            // 3. Cập nhật trạng thái giường
            if ($bedId) {
                $updateBedSql = "UPDATE Bed 
                               SET status = 'Trống' 
                               WHERE bed_id = '$bedId'";
                
                if (!mysqli_query($this->conn, $updateBedSql)) {
                    throw new Exception('Lỗi cập nhật giường: ' . mysqli_error($this->conn));
                }
            }
            
            // 4. Cập nhật available_beds của phòng
            $updateRoomSql = "UPDATE Room 
                            SET available_beds = available_beds + 1,
                                status = CASE 
                                    WHEN available_beds + 1 > 0 THEN 'Trống'
                                    ELSE status
                                END
                            WHERE room_id = '$roomId'";
            
            if (!mysqli_query($this->conn, $updateRoomSql)) {
                throw new Exception('Lỗi cập nhật phòng: ' . mysqli_error($this->conn));
            }
            
            // 5. Cập nhật hợp đồng
            $updateContractSql = "UPDATE Contract 
                                SET status = 'Đã hủy',
                                    end_date = CURDATE()
                                WHERE registration_id = '$registrationId'";
            
            mysqli_query($this->conn, $updateContractSql);
            
            mysqli_commit($this->conn);
            
            return [
                'success' => true,
                'message' => 'Xóa sinh viên khỏi phòng thành công'
            ];
            
        } catch (Exception $e) {
            mysqli_rollback($this->conn);
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Lấy danh sách giường trống trong phòng
     */
    public function getAvailableBeds($roomId) {
        $sql = "SELECT bed_id, bed_number, status
                FROM Bed
                WHERE room_id = '$roomId'
                AND status = 'Trống'
                ORDER BY bed_number ASC";
        
        $result = mysqli_query($this->conn, $sql);
        
        if (!$result) {
            return [
                'success' => false,
                'message' => 'Lỗi truy vấn: ' . mysqli_error($this->conn)
            ];
        }
        
        $beds = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $beds[] = $row;
        }
        
        return [
            'success' => true,
            'data' => $beds
        ];
    }
    
    /**
     * Chuyển sinh viên sang giường khác
     */
    public function transferStudentBed($registrationId, $newBedId) {
        mysqli_begin_transaction($this->conn);
        
        try {
            // 1. Lấy thông tin đăng ký hiện tại
            $regSql = "SELECT rr.room_id, rr.bed_id as old_bed_id, rr.student_id
                      FROM RoomRegistration rr
                      WHERE rr.registration_id = '$registrationId'
                      AND rr.status = 'Đã duyệt'";
            $regResult = mysqli_query($this->conn, $regSql);
            $regData = mysqli_fetch_assoc($regResult);
            
            if (!$regData) {
                throw new Exception('Không tìm thấy đăng ký');
            }
            
            $oldBedId = $regData['old_bed_id'];
            $roomId = $regData['room_id'];
            
            // 2. Kiểm tra giường mới có trống không
            $bedSql = "SELECT bed_id, status, room_id
                      FROM Bed
                      WHERE bed_id = '$newBedId'
                      AND room_id = '$roomId'";
            $bedResult = mysqli_query($this->conn, $bedSql);
            $bedData = mysqli_fetch_assoc($bedResult);
            
            if (!$bedData) {
                throw new Exception('Giường không tồn tại hoặc không thuộc phòng này');
            }
            
            if ($bedData['status'] !== 'Trống') {
                throw new Exception('Giường mới đã có người sử dụng');
            }
            
            // 3. Cập nhật giường cũ thành trống
            if ($oldBedId) {
                $updateOldBedSql = "UPDATE Bed 
                                   SET status = 'Trống' 
                                   WHERE bed_id = '$oldBedId'";
                
                if (!mysqli_query($this->conn, $updateOldBedSql)) {
                    throw new Exception('Lỗi cập nhật giường cũ: ' . mysqli_error($this->conn));
                }
            }
            
            // 4. Cập nhật giường mới thành đang sử dụng
            $updateNewBedSql = "UPDATE Bed 
                               SET status = 'Đang sử dụng' 
                               WHERE bed_id = '$newBedId'";
            
            if (!mysqli_query($this->conn, $updateNewBedSql)) {
                throw new Exception('Lỗi cập nhật giường mới: ' . mysqli_error($this->conn));
            }
            
            // 5. Cập nhật registration với giường mới
            $updateRegSql = "UPDATE RoomRegistration 
                           SET bed_id = '$newBedId'
                           WHERE registration_id = '$registrationId'";
            
            if (!mysqli_query($this->conn, $updateRegSql)) {
                throw new Exception('Lỗi cập nhật đăng ký: ' . mysqli_error($this->conn));
            }
            
            mysqli_commit($this->conn);
            
            return [
                'success' => true,
                'message' => 'Chuyển giường thành công'
            ];
            
        } catch (Exception $e) {
            mysqli_rollback($this->conn);
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Thêm phòng mới
     */
    public function createRoom($data) {
        // Validate dữ liệu
        $required = ['building', 'floor','room_name', 'capacity', 'price_per_month'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || $data[$field] === '') {
                return [
                    'success' => false,
                    'message' => "Thiếu trường: $field"
                ];
            }
        }
        
        $building = mysqli_real_escape_string($this->conn, $data['building']);
        $floor = intval($data['floor']);
        $room_name = intval($data['room_name']);
        $capacity = intval($data['capacity']);
        $genderRestriction = isset($data['gender_restriction']) ? mysqli_real_escape_string($this->conn, $data['gender_restriction']) : NULL;
        $pricePerMonth = floatval($data['price_per_month']);
        $status = isset($data['status']) ? mysqli_real_escape_string($this->conn, $data['status']) : 'Trống';
        
        $genderSQL = $genderRestriction ? "'$genderRestriction'" : "NULL";
        $sql = "INSERT INTO Room (room_name,building, floor, capacity, available_beds, gender_restriction, price_per_month, status)
                VALUES ($room_name,'$building', $floor, $capacity, $capacity, $genderSQL, $pricePerMonth, '$status')";
        
        try {
            // Thử thực hiện truy vấn
            $result = mysqli_query($this->conn, $sql);

            // Trường hợp 1: Strict Mode TẮT (mysqli_query trả về false)
            if (!$result) {
                if (mysqli_errno($this->conn) == 1062) {
                    throw new Exception("Phòng số $room_name đã tồn tại tại tòa $building (Trùng lặp).", 1062);
                }
                throw new Exception(mysqli_error($this->conn));
            }

        } catch (Exception $e) {
            // Trường hợp 2: Strict Mode BẬT (mysqli_query ném Exception)
            // Hoặc bắt lỗi từ block try ở trên
            
            // Kiểm tra mã lỗi 1062 (Duplicate entry)
            if ($e->getCode() == 1062 || mysqli_errno($this->conn) == 1062) {
                return [
                    'success' => false,
                    'message' => "Phòng số $room_name đã tồn tại tại tòa $building. Vui lòng kiểm tra lại!"
                ];
            }

            return [
                'success' => false,
                'message' => 'Lỗi tạo phòng: ' . $e->getMessage()
            ];
        }
        
        $roomId = mysqli_insert_id($this->conn);
        
        // Giường sẽ được tạo tự động bởi trigger trong database
        return [
            'success' => true,
            'message' => 'Thêm phòng thành công',
            'data' => ['room_id' => $roomId]
        ];
    }
    
    /**
     * Cập nhật thông tin phòng
     */
    public function updateRoom($roomId, $data) {
        // Kiểm tra phòng có tồn tại không
        $checkSql = "SELECT room_id, capacity FROM Room WHERE room_id = $roomId";
        $checkResult = mysqli_query($this->conn, $checkSql);
        
        if (mysqli_num_rows($checkResult) == 0) {
            return [
                'success' => false,
                'message' => 'Phòng không tồn tại'
            ];
        }
        
        $oldRoom = mysqli_fetch_assoc($checkResult);
        $oldCapacity = $oldRoom['capacity'];
        
        mysqli_begin_transaction($this->conn);
        
        try {
            // Xây dựng câu UPDATE
            $updates = [];
            
            if (isset($data['building'])) {
                $building = mysqli_real_escape_string($this->conn, $data['building']);
                $updates[] = "building = '$building'";
            }
            
            if (isset($data['floor'])) {
                $floor = intval($data['floor']);
                $updates[] = "floor = $floor";
            }

            if (isset($data['room_name'])) {
                $room_name = intval($data['room_name']);
                $updates[] = "room_name = $room_name";
            }
            
            if (isset($data['capacity'])) {
                $newCapacity = intval($data['capacity']);
                
                // Kiểm tra số người đang ở
                $occupiedSql = "SELECT COUNT(*) as occupied FROM Bed WHERE room_id = $roomId AND status = 'Đang sử dụng'";
                $occupiedResult = mysqli_query($this->conn, $occupiedSql);
                $occupiedData = mysqli_fetch_assoc($occupiedResult);
                $occupied = $occupiedData['occupied'];
                
                if ($newCapacity < $occupied) {
                    throw new Exception("Không thể giảm sức chứa xuống $newCapacity vì đang có $occupied người ở");
                }
                
                // $updates[] = "capacity = $newCapacity";
                // $updates[] = "available_beds = $newCapacity - $occupied";

                $updateCapSql = "UPDATE Room 
                    SET capacity = $newCapacity,
                        available_beds = ($newCapacity - $occupied)
                    WHERE room_id = $roomId";

                if (!mysqli_query($this->conn, $updateCapSql)) {
                    throw new Exception("Lỗi cập nhật sức chứa: " . mysqli_error($this->conn));
                }
                
                // Cập nhật số lượng giường
                if ($newCapacity > $oldCapacity) {
                    // Thêm giường
                    for ($i = $oldCapacity + 1; $i <= $newCapacity; $i++) {
                        $bedSql = "INSERT INTO Bed (room_id, bed_number, status) VALUES ($roomId, $i, 'Trống')";
                        if (!mysqli_query($this->conn, $bedSql)) {
                            throw new Exception('Lỗi thêm giường: ' . mysqli_error($this->conn));
                        }
                    }
                } elseif ($newCapacity < $oldCapacity) {
                    // Xóa giường trống thừa
                    $deleteBedSql = "DELETE FROM Bed WHERE room_id = $roomId AND bed_number > $newCapacity AND status = 'Trống'";
                    if (!mysqli_query($this->conn, $deleteBedSql)) {
                        throw new Exception('Lỗi xóa giường: ' . mysqli_error($this->conn));
                    }
                }
            }
            
            if (isset($data['gender_restriction'])) {
                $gender = $data['gender_restriction'];
                if ($gender === '' || $gender === null) {
                    $updates[] = "gender_restriction = NULL";
                } else {
                    $gender = mysqli_real_escape_string($this->conn, $gender);
                    $updates[] = "gender_restriction = '$gender'";
                }
            }
            
            if (isset($data['price_per_month'])) {
                $price = floatval($data['price_per_month']);
                $updates[] = "price_per_month = $price";
            }
            
            if (isset($data['status'])) {
                $status = mysqli_real_escape_string($this->conn, $data['status']);
                $updates[] = "status = '$status'";
            }
            
            
                if (empty($updates)) {
                    return [
                        'success' => false,
                        'message' => 'Không có dữ liệu để cập nhật'
                    ];
                }
                
                $sql = "UPDATE Room SET " . implode(', ', $updates) . " WHERE room_id = $roomId";
                
                $result = @mysqli_query($this->conn, $sql);
                if (!$result) {
                    // Nếu query thất bại (trả về false), ném lỗi để xuống catch xử lý chung
                    throw new Exception(mysqli_error($this->conn), mysqli_errno($this->conn));
                }
                // --- KẾT THÚC ĐOẠN SỬA ---
                
                mysqli_commit($this->conn);
                
                return [
                    'success' => true,
                    'message' => 'Cập nhật phòng thành công'
                ];
                
        } catch (Exception $e) {
                mysqli_rollback($this->conn);
                
                // --- BẮT ĐẦU ĐOẠN SỬA PHẦN CATCH ---
                // Kiểm tra mã lỗi 1062 ở đây
                if ($e->getCode() == 1062 || mysqli_errno($this->conn) == 1062) {
                    return [
                        'success' => false,
                        'message' => "Tên phòng này đã tồn tại trong tòa nhà!"
                    ];
                }
                
                return [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }
    }
    
    /**
     * Xóa phòng
     */
    public function deleteRoom($roomId) {
        // Kiểm tra phòng có người ở không
        $checkSql = "SELECT COUNT(*) as count 
                    FROM RoomRegistration 
                    WHERE room_id = $roomId 
                    AND status = 'Đã duyệt'
                    AND end_date >= CURDATE()";
        $checkResult = mysqli_query($this->conn, $checkSql);
        $checkData = mysqli_fetch_assoc($checkResult);
        
        if ($checkData['count'] > 0) {
            return [
                'success' => false,
                'message' => 'Không thể xóa phòng đang có người ở'
            ];
        }
        
        mysqli_begin_transaction($this->conn);
        
        try {
            // Xóa giường (CASCADE sẽ tự động xóa)
            $deleteBedSql = "DELETE FROM Bed WHERE room_id = $roomId";
            if (!mysqli_query($this->conn, $deleteBedSql)) {
                throw new Exception('Lỗi xóa giường: ' . mysqli_error($this->conn));
            }
            
            // Xóa phòng
            $deleteRoomSql = "DELETE FROM Room WHERE room_id = $roomId";
            if (!mysqli_query($this->conn, $deleteRoomSql)) {
                throw new Exception('Lỗi xóa phòng: ' . mysqli_error($this->conn));
            }
            
            mysqli_commit($this->conn);
            
            return [
                'success' => true,
                'message' => 'Xóa phòng thành công'
            ];
            
        } catch (Exception $e) {
            mysqli_rollback($this->conn);
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Lấy thông tin phòng theo ID
     */
    public function getRoomById($roomId) {
        $sql = "SELECT * FROM Room WHERE room_id = $roomId";
        $result = mysqli_query($this->conn, $sql);
        
        if (!$result) {
            return [
                'success' => false,
                'message' => 'Lỗi truy vấn: ' . mysqli_error($this->conn)
            ];
        }
        
        $room = mysqli_fetch_assoc($result);
        
        if (!$room) {
            return [
                'success' => false,
                'message' => 'Không tìm thấy phòng'
            ];
        }
        
        return [
            'success' => true,
            'data' => $room
        ];
    }
}
