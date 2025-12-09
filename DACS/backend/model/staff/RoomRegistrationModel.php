<?php
include_once __DIR__ . '/../database.php';

class RoomRegistrationModel {
    private $conn;
    
    function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    private function hasApprovedRegistrationBefore($studentId) {
        $studentId = mysqli_real_escape_string($this->conn, $studentId);
        $sql = "SELECT COUNT(*) as cnt FROM RoomRegistration 
                WHERE student_id = '$studentId' 
                  AND status = 'Đã duyệt'";
        $result = mysqli_query($this->conn, $sql);
        if (!$result) {
            return false;
        }
        $row = mysqli_fetch_assoc($result);
        return ($row['cnt'] ?? 0) > 0;
    }

    private function createInitialRoomPayment($registration, $contractId) {
        if (empty($registration['student_id']) || empty($registration['price_per_month'])) {
            return;
        }

        $studentId = mysqli_real_escape_string($this->conn, $registration['student_id']);
        $contractId = (int)$contractId;
        $amount = (float)$registration['price_per_month'];
        $paymentDate = mysqli_real_escape_string($this->conn, $registration['start_date']);
        $description = mysqli_real_escape_string(
            $this->conn,
            'Tiền phòng tháng ' . date('m/Y', strtotime($registration['start_date'])) . ' (đăng ký mới)'
        );

        $sql = "INSERT INTO Payment (student_id, contract_id, payment_type, amount, payment_date, payment_method, description, status)
                VALUES ('$studentId', $contractId, 'Phòng', $amount, '$paymentDate', 'Chuyển khoản online', '$description', 'Chưa thanh toán')";

        if (!mysqli_query($this->conn, $sql)) {
            throw new Exception('Lỗi tạo hóa đơn tháng đầu: ' . mysqli_error($this->conn));
        }
    }
    
    /**
     * Lấy danh sách đăng ký phòng (có thể lọc theo trạng thái)
     */
    public function getAllRegistrations($status = null) {
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
                    rr.guest_info,
                    u.user_id,
                    u.full_name,
                    u.email,
                    u.phone,
                    s.faculty,
                    s.major,
                    s.gender,
                    s.address,
                    r.building,
                    r.room_name,
                    r.floor,
                    r.capacity,
                    r.available_beds,
                    r.price_per_month,
                    b.bed_number,
                    approver.full_name as approver_name
                FROM RoomRegistration rr
                LEFT JOIN Users u ON rr.student_id = u.user_id
                LEFT JOIN Student s ON u.user_id = s.user_id
                LEFT JOIN Room r ON rr.room_id = r.room_id
                LEFT JOIN Bed b ON b.bed_id = rr.bed_id
                LEFT JOIN Staff st ON rr.approved_by = st.staff_id
                LEFT JOIN Users approver ON st.staff_id = approver.user_id";
        
        if ($status !== null) {
            $sql .= " WHERE rr.status = '" . mysqli_real_escape_string($this->conn, $status) . "'";
        }
        
        $sql .= " ORDER BY rr.request_date DESC";
        
        error_log("=== DEBUG getAllRegistrations ===");
        error_log("SQL: " . $sql);
        
        $result = mysqli_query($this->conn, $sql);
        
        if (!$result) {
            error_log("Query ERROR: " . mysqli_error($this->conn));
            return [
                'success' => false,
                'message' => 'Lỗi truy vấn: ' . mysqli_error($this->conn)
            ];
        }
        
        $registrations = [];
        while ($row = mysqli_fetch_assoc($result)) {
            // Nếu là khách (không có student_id), lấy thông tin từ guest_info
            if (empty($row['student_id']) && !empty($row['guest_info'])) {
                $guestInfo = json_decode($row['guest_info'], true);
                if ($guestInfo) {
                    $row['full_name'] = $guestInfo['full_name'] ?? '';
                    $row['email'] = $guestInfo['email'] ?? '';
                    $row['phone'] = $guestInfo['phone'] ?? '';
                    $row['faculty'] = $guestInfo['faculty'] ?? '';
                    $row['is_guest'] = true;
                }
            }
            $registrations[] = $row;
        }
        
        return [
            'success' => true,
            'data' => $registrations
        ];
    }
    
    /**
     * Lấy chi tiết một đăng ký cụ thể
     */
    public function getRegistrationById($registrationId) {
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
                    rr.guest_info,
                    u.user_id,
                    u.full_name,
                    u.email,
                    u.phone,
                    u.role,
                    s.faculty,
                    s.major,
                    s.gender,
                    s.address,
                    s.date_of_birth,
                    r.building,
                    r.floor,
                    r.capacity,
                    r.available_beds,
                    r.price_per_month,
                    r.gender_restriction,
                    b.bed_number,
                    b.status as bed_status,
                    approver.full_name as approver_name
                FROM RoomRegistration rr
                LEFT JOIN Users u ON rr.student_id = u.user_id
                LEFT JOIN Student s ON u.user_id = s.user_id
                LEFT JOIN Room r ON rr.room_id = r.room_id
                LEFT JOIN Bed b ON b.bed_id = rr.bed_id
                LEFT JOIN Staff st ON rr.approved_by = st.staff_id
                LEFT JOIN Users approver ON st.staff_id = approver.user_id
                WHERE rr.registration_id = '$registrationId'";
        
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
                'message' => 'Không tìm thấy đăng ký'
            ];
        }
        
        // Nếu là khách (không có student_id), lấy thông tin từ guest_info
        if (empty($data['student_id']) && !empty($data['guest_info'])) {
            $guestInfo = json_decode($data['guest_info'], true);
            if ($guestInfo) {
                $data['full_name'] = $guestInfo['full_name'] ?? '';
                $data['email'] = $guestInfo['email'] ?? '';
                $data['phone'] = $guestInfo['phone'] ?? '';
                $data['faculty'] = $guestInfo['faculty'] ?? '';
                $data['is_guest'] = true;
            }
        }
        
        return [
            'success' => true,
            'data' => $data
        ];
    }
    
    /**
     * Duyệt đăng ký phòng
     */
    public function approveRegistration($registrationId, $approvedBy) {
        // Bắt đầu transaction
        mysqli_begin_transaction($this->conn);
        
        try {
            // Lấy thông tin đăng ký
            $regInfo = $this->getRegistrationById($registrationId);
            if (!$regInfo['success']) {
                throw new Exception($regInfo['message']);
            }
            
            $registration = $regInfo['data'];
            
            // Nếu đã được duyệt rồi, không cần làm gì
            if ($registration['status'] === 'Đã duyệt') {
                throw new Exception('Đăng ký này đã được duyệt rồi');
            }
            
            // Nếu đang ở trạng thái "Từ chối", cho phép chuyển sang "Đã duyệt"
            // Không cần kiểm tra trạng thái cũ nữa
            
            // Kiểm tra bed_id có tồn tại không
            if (empty($registration['bed_id'])) {
                throw new Exception('Đăng ký chưa được chọn giường');
            }
            
            // Kiểm tra giường còn trống không
            $bedCheckSql = "SELECT status FROM Bed WHERE bed_id = '{$registration['bed_id']}'";
            $bedCheckResult = mysqli_query($this->conn, $bedCheckSql);
            $bedRow = mysqli_fetch_assoc($bedCheckResult);
            
            if (!$bedRow) {
                throw new Exception('Không tìm thấy giường');
            }
            
            if ($bedRow['status'] !== 'Trống') {
                throw new Exception('Giường đã được đặt bởi người khác');
            }
            
            // Kiểm tra phòng còn chỗ không
            $roomCheckSql = "SELECT available_beds FROM Room WHERE room_id = '{$registration['room_id']}'";
            $roomCheckResult = mysqli_query($this->conn, $roomCheckSql);
            $roomRow = mysqli_fetch_assoc($roomCheckResult);
            
            if (!$roomRow || $roomRow['available_beds'] <= 0) {
                throw new Exception('Phòng đã hết chỗ trống');
            }
            
            $isFirstApprovedRegistration = !empty($registration['student_id'])
                ? !$this->hasApprovedRegistrationBefore($registration['student_id'])
                : false;

            // Cập nhật trạng thái đăng ký
            $updateRegSql = "UPDATE RoomRegistration 
                            SET status = 'Đã duyệt', 
                                approved_by = '$approvedBy'
                            WHERE registration_id = '$registrationId'";
            
            if (!mysqli_query($this->conn, $updateRegSql)) {
                throw new Exception('Lỗi cập nhật trạng thái đăng ký: ' . mysqli_error($this->conn));
            }
            
            // Cập nhật trạng thái giường
            $updateBedSql = "UPDATE Bed 
                            SET status = 'Đang sử dụng'
                            WHERE bed_id = '{$registration['bed_id']}'";
            
            if (!mysqli_query($this->conn, $updateBedSql)) {
                throw new Exception('Lỗi cập nhật trạng thái giường: ' . mysqli_error($this->conn));
            }
            
            // Tính ngày hết hạn hợp đồng = 1 tháng sau ngày bắt đầu
            $contractEndDate = date('Y-m-d', strtotime($registration['start_date'] . ' +1 month'));

            // Đồng bộ ngày kết thúc của đăng ký phòng
            $updateRegistrationEndSql = "UPDATE RoomRegistration 
                                          SET end_date = '$contractEndDate'
                                          WHERE registration_id = '$registrationId'";
            if (!mysqli_query($this->conn, $updateRegistrationEndSql)) {
                throw new Exception('Lỗi cập nhật ngày hết hạn đăng ký: ' . mysqli_error($this->conn));
            }

            // Giảm số giường trống trong phòng
            $updateRoomSql = "UPDATE Room 
                             SET available_beds = available_beds - 1
                             WHERE room_id = '{$registration['room_id']}'";
            
            if (!mysqli_query($this->conn, $updateRoomSql)) {
                throw new Exception('Lỗi cập nhật số giường trống: ' . mysqli_error($this->conn));
            }
            
            // Tạo hợp đồng
            $createContractSql = "INSERT INTO Contract 
                                 (registration_id, created_date, end_date, status) 
                                 VALUES 
                                 ('$registrationId', 
                                  '{$registration['start_date']}', 
                                  '$contractEndDate', 
                                  'Hiệu lực')";
            
            if (!mysqli_query($this->conn, $createContractSql)) {
                throw new Exception('Lỗi tạo hợp đồng: ' . mysqli_error($this->conn));
            }

            $contractId = mysqli_insert_id($this->conn);

            if ($isFirstApprovedRegistration) {
                $this->createInitialRoomPayment($registration, $contractId);
            }
            
            // Commit transaction
            mysqli_commit($this->conn);
            
            return [
                'success' => true,
                'message' => 'Đã duyệt đăng ký thành công'
            ];
            
        } catch (Exception $e) {
            // Rollback nếu có lỗi
            mysqli_rollback($this->conn);
            
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Từ chối đăng ký phòng
     */
    public function rejectRegistration($registrationId, $rejectedBy) {
        // Bắt đầu transaction
        mysqli_begin_transaction($this->conn);
        
        try {
            // Kiểm tra trạng thái hiện tại
            $regInfo = $this->getRegistrationById($registrationId);
            if (!$regInfo['success']) {
                throw new Exception($regInfo['message']);
            }
            
            $registration = $regInfo['data'];
            
            // Nếu đã bị từ chối rồi, không cần làm gì
            if ($registration['status'] === 'Từ chối') {
                throw new Exception('Đăng ký này đã bị từ chối rồi');
            }
            
            // Nếu đang ở trạng thái "Đã duyệt", cần hoàn tác các thay đổi
            if ($registration['status'] === 'Đã duyệt') {
                // Cập nhật trạng thái giường về trống
                if (!empty($registration['bed_id'])) {
                    $updateBedSql = "UPDATE Bed 
                                    SET status = 'Trống'
                                    WHERE bed_id = '{$registration['bed_id']}'";
                    
                    if (!mysqli_query($this->conn, $updateBedSql)) {
                        throw new Exception('Lỗi cập nhật trạng thái giường: ' . mysqli_error($this->conn));
                    }
                }
                
                // Tăng số giường trống trong phòng
                $updateRoomSql = "UPDATE Room 
                                 SET available_beds = available_beds + 1
                                 WHERE room_id = '{$registration['room_id']}'";
                
                if (!mysqli_query($this->conn, $updateRoomSql)) {
                    throw new Exception('Lỗi cập nhật số giường trống: ' . mysqli_error($this->conn));
                }
                
                // Xóa hợp đồng nếu có
                $deleteContractSql = "DELETE FROM Contract 
                                     WHERE registration_id = '$registrationId'";
                
                if (!mysqli_query($this->conn, $deleteContractSql)) {
                    throw new Exception('Lỗi xóa hợp đồng: ' . mysqli_error($this->conn));
                }
            }
            
            // Cập nhật trạng thái sang "Từ chối"
            $sql = "UPDATE RoomRegistration 
                    SET status = 'Từ chối', 
                        approved_by = '$rejectedBy'
                    WHERE registration_id = '$registrationId'";
            
            if (!mysqli_query($this->conn, $sql)) {
                throw new Exception('Lỗi cập nhật trạng thái: ' . mysqli_error($this->conn));
            }
            
            // Commit transaction
            mysqli_commit($this->conn);
            
            return [
                'success' => true,
                'message' => 'Đã từ chối đăng ký'
            ];
            
        } catch (Exception $e) {
            // Rollback nếu có lỗi
            mysqli_rollback($this->conn);
            
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Đếm số lượng đăng ký theo trạng thái
     */
    public function countByStatus($status) {
        $sql = "SELECT COUNT(*) as count 
                FROM RoomRegistration 
                WHERE status = '" . mysqli_real_escape_string($this->conn, $status) . "'";
        
        $result = mysqli_query($this->conn, $sql);
        
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            return $row['count'];
        }
        
        return 0;
    }
    
    /**
     * Lấy thống kê tổng quan
     */
    public function getStatistics() {
        return [
            'pending' => $this->countByStatus('Chờ duyệt'),
            'approved' => $this->countByStatus('Đã duyệt'),
            'rejected' => $this->countByStatus('Từ chối'),
            'checkout' => $this->countByStatus('Đã trả phòng')
        ];
    }
    
    /**
     * Đặt lại trạng thái đăng ký về chờ duyệt
     */
    public function resetToPending($registrationId) {
        // Bắt đầu transaction
        mysqli_begin_transaction($this->conn);
        
        try {
            // Lấy thông tin đăng ký
            $regInfo = $this->getRegistrationById($registrationId);
            if (!$regInfo['success']) {
                throw new Exception($regInfo['message']);
            }
            
            $registration = $regInfo['data'];
            
            // Chỉ cho phép đặt lại nếu đã được duyệt hoặc từ chối
            if ($registration['status'] === 'Chờ duyệt') {
                throw new Exception('Đăng ký này đang ở trạng thái chờ duyệt');
            }
            
            // Nếu đăng ký đã được duyệt, cần hoàn tác các thay đổi
            if ($registration['status'] === 'Đã duyệt') {
                // Cập nhật trạng thái giường về trống
                if (!empty($registration['bed_id'])) {
                    $updateBedSql = "UPDATE Bed 
                                    SET status = 'Trống'
                                    WHERE bed_id = '{$registration['bed_id']}'";
                    
                    if (!mysqli_query($this->conn, $updateBedSql)) {
                        throw new Exception('Lỗi cập nhật trạng thái giường: ' . mysqli_error($this->conn));
                    }
                }
                
                // Tăng số giường trống trong phòng
                $updateRoomSql = "UPDATE Room 
                                 SET available_beds = available_beds + 1
                                 WHERE room_id = '{$registration['room_id']}'";
                
                if (!mysqli_query($this->conn, $updateRoomSql)) {
                    throw new Exception('Lỗi cập nhật số giường trống: ' . mysqli_error($this->conn));
                }
                
                // Xóa hợp đồng nếu có
                $deleteContractSql = "DELETE FROM Contract 
                                     WHERE registration_id = '$registrationId'";
                
                if (!mysqli_query($this->conn, $deleteContractSql)) {
                    throw new Exception('Lỗi xóa hợp đồng: ' . mysqli_error($this->conn));
                }
            }
            
            // Cập nhật trạng thái đăng ký về chờ duyệt
            $updateRegSql = "UPDATE RoomRegistration 
                            SET status = 'Chờ duyệt', 
                                approved_by = NULL
                            WHERE registration_id = '$registrationId'";
            
            if (!mysqli_query($this->conn, $updateRegSql)) {
                throw new Exception('Lỗi cập nhật trạng thái đăng ký: ' . mysqli_error($this->conn));
            }
            
            // Commit transaction
            mysqli_commit($this->conn);
            
            return [
                'success' => true,
                'message' => 'Đã đặt lại trạng thái về chờ duyệt'
            ];
            
        } catch (Exception $e) {
            // Rollback nếu có lỗi
            mysqli_rollback($this->conn);
            
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
