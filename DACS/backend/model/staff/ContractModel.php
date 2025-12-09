<?php 
include_once __DIR__ . '/../database.php';

class ContractModel {
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }   

    /**
     * Lấy danh sách tất cả hợp đồng với thông tin chi tiết
     */
    public function getAllContracts() {
        $this->processExpiredContracts();
        $sql = "
            SELECT 
                c.contract_id,
                c.registration_id,
                c.created_date,
                c.end_date,
                c.status,
                u.user_id,
                u.full_name as student_name,
                u.email as student_email,
                u.phone as student_phone,
                s.student_id as student_code,
                s.faculty,
                s.major,
                r.room_id,
                CONCAT('Tòa ', rm.building, ' - Tầng ', rm.floor, ' - Phòng ', rm.room_name) as room_info,
                rm.price_per_month,
                rr.start_date,
                rr.end_date as registration_end_date
            FROM Contract c
            INNER JOIN RoomRegistration rr ON c.registration_id = rr.registration_id
            INNER JOIN Student s ON rr.student_id = s.user_id
            INNER JOIN Users u ON s.user_id = u.user_id
            INNER JOIN Room r ON rr.room_id = r.room_id
            INNER JOIN Room rm ON rr.room_id = rm.room_id
            ORDER BY c.created_date DESC
        ";
        
        $result = mysqli_query($this->conn, $sql);
        
        if (!$result) {
            return ["status" => "error", "message" => mysqli_error($this->conn)];
        }

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }

        return ["status" => "success", "data" => $data];
    }


    /**
     * Lấy thông tin chi tiết 1 hợp đồng theo ID
     */
    public function getContractById($contract_id) {
        $sql = "
            SELECT 
                c.contract_id,
                c.registration_id,
                c.created_date,
                c.end_date,
                c.status,
                u.user_id,
                u.full_name as student_name,
                u.email as student_email,
                u.phone as student_phone,
                u.username,
                s.student_id as student_code,
                s.faculty,
                s.major,
                s.gender,
                s.date_of_birth,
                s.address,
                r.room_id,
                rm.building,
                rm.floor,
                rm.capacity,
                rm.available_beds,
                rm.gender_restriction,
                rm.price_per_month,
                rm.status as room_status,
                rr.start_date,
                rr.end_date as registration_end_date,
                rr.status as registration_status,
                rr.approved_by,
                staff.full_name as approved_by_name
            FROM Contract c
            INNER JOIN RoomRegistration rr ON c.registration_id = rr.registration_id
            INNER JOIN Student s ON rr.student_id = s.user_id
            INNER JOIN Users u ON s.user_id = u.user_id
            INNER JOIN Room r ON rr.room_id = r.room_id
            INNER JOIN Room rm ON rr.room_id = rm.room_id
            LEFT JOIN Staff st ON rr.approved_by = st.staff_id
            LEFT JOIN Users staff ON st.staff_id = staff.user_id
            WHERE c.contract_id = ?
        ";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $contract_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (!$result) {
            return ["status" => "error", "message" => mysqli_error($this->conn)];
        }
        
        $data = mysqli_fetch_assoc($result);
        
        if ($data) {
            return ["status" => "success", "data" => $data];
        } else {
            return ["status" => "error", "message" => "Không tìm thấy hợp đồng"];
        }
    }

    /**
     * Cập nhật thông tin hợp đồng (gia hạn thời gian)
     */
    public function updateContract($data) {
        $contract_id = $data['contract_id'];
        $end_date = $data['end_date'];
        $status = $data['status'];
        
        $sql = "UPDATE Contract 
                SET end_date = ?, 
                    status = ?
                WHERE contract_id = ?";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssi", $end_date, $status, $contract_id);
        
        if (mysqli_stmt_execute($stmt)) {
            // Cập nhật cả thông tin trong RoomRegistration nếu cần
            if (isset($data['update_registration']) && $data['update_registration']) {
                $registration_sql = "UPDATE RoomRegistration rr
                                    INNER JOIN Contract c ON rr.registration_id = c.registration_id
                                    SET rr.end_date = ?
                                    WHERE c.contract_id = ?";
                $reg_stmt = mysqli_prepare($this->conn, $registration_sql);
                mysqli_stmt_bind_param($reg_stmt, "si", $end_date, $contract_id);
                mysqli_stmt_execute($reg_stmt);
            }
            
            return ["status" => "success", "message" => "Cập nhật hợp đồng thành công"];
        } else {
            return ["status" => "error", "message" => mysqli_error($this->conn)];
        }
    }

    /**
     * Chuyển phòng cho sinh viên (tạo đăng ký mới và hợp đồng mới)
     */
    public function changeRoom($data) {
        $contract_id = $data['contract_id'];
        $new_room_id = $data['new_room_id'];
        $start_date = $data['start_date'];
        $end_date = $data['end_date'];
        
        mysqli_begin_transaction($this->conn);
        
        try {
            // Lấy thông tin hợp đồng cũ
            $old_contract = $this->getContractById($contract_id);
            if ($old_contract['status'] !== 'success') {
                throw new Exception("Không tìm thấy hợp đồng");
            }
            
            $student_id = $old_contract['data']['user_id'];
            
            // Chấm dứt hợp đồng cũ
            $cancel_sql = "UPDATE Contract SET status = 'Đã hủy' WHERE contract_id = ?";
            $stmt1 = mysqli_prepare($this->conn, $cancel_sql);
            mysqli_stmt_bind_param($stmt1, "i", $contract_id);
            mysqli_stmt_execute($stmt1);
            
            // Tìm giường trống trong phòng mới
            $bed_sql = "SELECT bed_id FROM Bed WHERE room_id = ? AND status = 'Trống' LIMIT 1";
            $stmt_bed = mysqli_prepare($this->conn, $bed_sql);
            mysqli_stmt_bind_param($stmt_bed, "i", $new_room_id);
            mysqli_stmt_execute($stmt_bed);
            $bed_result = mysqli_stmt_get_result($stmt_bed);
            $bed_data = mysqli_fetch_assoc($bed_result);
            
            if (!$bed_data) {
                throw new Exception("Không tìm thấy giường trống trong phòng mới");
            }
            
            $new_bed_id = $bed_data['bed_id'];
            
            // Tạo đăng ký phòng mới với bed_id
            $reg_sql = "INSERT INTO RoomRegistration (student_id, room_id, bed_id, start_date, end_date, status, request_date, approved_by) 
                        VALUES (?, ?, ?, ?, ?, 'Đã duyệt', NOW(), ?)";
            $stmt2 = mysqli_prepare($this->conn, $reg_sql);
            $approved_by = isset($data['approved_by']) ? $data['approved_by'] : null;
            mysqli_stmt_bind_param($stmt2, "iiissi", $student_id, $new_room_id, $new_bed_id, $start_date, $end_date, $approved_by);
            mysqli_stmt_execute($stmt2);
            
            $new_registration_id = mysqli_insert_id($this->conn);
            
            // Cập nhật trạng thái giường mới
            $update_bed_sql = "UPDATE Bed SET status = 'Đang sử dụng' WHERE bed_id = ?";
            $stmt_update_bed = mysqli_prepare($this->conn, $update_bed_sql);
            mysqli_stmt_bind_param($stmt_update_bed, "i", $new_bed_id);
            mysqli_stmt_execute($stmt_update_bed);
            
            // Giải phóng giường cũ
            $old_bed_sql = "UPDATE Bed SET status = 'Trống' 
                           WHERE bed_id = (SELECT bed_id FROM RoomRegistration WHERE registration_id = ?)";
            $stmt_old_bed = mysqli_prepare($this->conn, $old_bed_sql);
            mysqli_stmt_bind_param($stmt_old_bed, "i", $old_registration_id);
            mysqli_stmt_execute($stmt_old_bed);
            
            // Tạo hợp đồng mới
            $contract_sql = "INSERT INTO Contract (registration_id, created_date, end_date, status) 
                            VALUES (?, ?, ?, 'Hiệu lực')";
            $stmt3 = mysqli_prepare($this->conn, $contract_sql);
            mysqli_stmt_bind_param($stmt3, "iss", $new_registration_id, $start_date, $end_date);
            mysqli_stmt_execute($stmt3);
            
            // Cập nhật số giường trống
            $update_old_room = "UPDATE Room SET available_beds = available_beds + 1 
                               WHERE room_id = (SELECT room_id FROM RoomRegistration WHERE registration_id = ?)";
            $stmt4 = mysqli_prepare($this->conn, $update_old_room);
            $old_registration_id = $old_contract['data']['registration_id'];
            mysqli_stmt_bind_param($stmt4, "i", $old_registration_id);
            mysqli_stmt_execute($stmt4);
            
            $update_new_room = "UPDATE Room SET available_beds = available_beds - 1 WHERE room_id = ?";
            $stmt5 = mysqli_prepare($this->conn, $update_new_room);
            mysqli_stmt_bind_param($stmt5, "i", $new_room_id);
            mysqli_stmt_execute($stmt5);
            
            mysqli_commit($this->conn);
            return ["status" => "success", "message" => "Chuyển phòng thành công"];
            
        } catch (Exception $e) {
            mysqli_rollback($this->conn);
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Chấm dứt/xóa hợp đồng
     */
    // public function terminateContract($contract_id, $reason = '') {
    //     mysqli_begin_transaction($this->conn);
        
    //     try {
    //         // Lấy thông tin hợp đồng
    //         $contract = $this->getContractById($contract_id);
    //         if ($contract['status'] !== 'success') {
    //             throw new Exception("Không tìm thấy hợp đồng");
    //         }
            
    //         // Cập nhật trạng thái hợp đồng
    //         $sql = "UPDATE Contract SET status = 'Đã hủy' WHERE contract_id = ?";
    //         $stmt = mysqli_prepare($this->conn, $sql);
    //         mysqli_stmt_bind_param($stmt, "i", $contract_id);
    //         mysqli_stmt_execute($stmt);
            
    //         // Cập nhật số giường trống
    //         $room_id = $contract['data']['room_id'];
    //         $update_room = "UPDATE Room SET available_beds = available_beds + 1 WHERE room_id = ?";
    //         $stmt2 = mysqli_prepare($this->conn, $update_room);
    //         mysqli_stmt_bind_param($stmt2, "i", $room_id);
    //         mysqli_stmt_execute($stmt2);
            
    //         mysqli_commit($this->conn);
    //         return ["status" => "success", "message" => "Chấm dứt hợp đồng thành công"];
            
    //     } catch (Exception $e) {
    //         mysqli_rollback($this->conn);
    //         return ["status" => "error", "message" => $e->getMessage()];
    //     }
    // }
/**
     * Chấm dứt hợp đồng (GIỮ LẠI LỊCH SỬ PAYMENT)
     * 1. Kiểm tra nợ: Nếu còn nợ -> Chặn.
     * 2. Hợp đồng -> 'Đã hủy'.
     * 3. Đăng ký -> 'Đã trả phòng'.
     * 4. Giường -> 'Trống'.
     * 5. KHÔNG XÓA PAYMENT (Để lưu lịch sử doanh thu).
     */
    public function terminateContract($contract_id, $reason = '') {
        // --- BƯỚC 1: KIỂM TRA CÔNG NỢ ---
        $sqlCheckDebt = "SELECT payment_type, amount 
                         FROM Payment 
                         WHERE contract_id = ? 
                         AND status IN ('Chưa thanh toán', 'Quá hạn')";
        
        $stmtCheck = mysqli_prepare($this->conn, $sqlCheckDebt);
        mysqli_stmt_bind_param($stmtCheck, "i", $contract_id);
        mysqli_stmt_execute($stmtCheck);
        $resultCheck = mysqli_stmt_get_result($stmtCheck);
        
        if (mysqli_num_rows($resultCheck) > 0) {
            $debts = [];
            $totalDebt = 0;
            while ($row = mysqli_fetch_assoc($resultCheck)) {
                $debts[] = $row['payment_type'];
                $totalDebt += $row['amount'];
            }
            return [
                "status" => "error", 
                "message" => "Không thể chấm dứt! Còn nợ: " . implode(", ", $debts) . ". Tổng: " . number_format($totalDebt) . "đ"
            ];
        }

        // --- BƯỚC 2: XỬ LÝ CHẤM DỨT ---
        mysqli_begin_transaction($this->conn);
        try {
            // Lấy thông tin
            $sqlInfo = "SELECT c.registration_id, rr.bed_id 
                        FROM Contract c
                        INNER JOIN RoomRegistration rr ON c.registration_id = rr.registration_id
                        WHERE c.contract_id = ?";
            $stmtInfo = mysqli_prepare($this->conn, $sqlInfo);
            mysqli_stmt_bind_param($stmtInfo, "i", $contract_id);
            mysqli_stmt_execute($stmtInfo);
            $info = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtInfo));

            if (!$info) throw new Exception("Không tìm thấy hợp đồng.");

            // Cập nhật Hợp đồng -> Đã hủy
            $sqlContract = "UPDATE Contract SET status = 'Đã hủy', end_date = CURDATE() WHERE contract_id = ?";
            $stmtContract = mysqli_prepare($this->conn, $sqlContract);
            mysqli_stmt_bind_param($stmtContract, "i", $contract_id);
            mysqli_stmt_execute($stmtContract);

            // Cập nhật Đăng ký -> Đã trả phòng
            $sqlReg = "UPDATE RoomRegistration SET status = 'Đã trả phòng', end_date = CURDATE() WHERE registration_id = ?";
            $stmtReg = mysqli_prepare($this->conn, $sqlReg);
            mysqli_stmt_bind_param($stmtReg, "i", $info['registration_id']);
            mysqli_stmt_execute($stmtReg);
            
            // Giải phóng giường
            if ($info['bed_id']) {
                $sqlBed = "UPDATE Bed SET status = 'Trống' WHERE bed_id = ?";
                $stmtBed = mysqli_prepare($this->conn, $sqlBed);
                mysqli_stmt_bind_param($stmtBed, "i", $info['bed_id']);
                mysqli_stmt_execute($stmtBed);
            }

            // LƯU Ý: Đã xóa đoạn DELETE FROM Payment ở đây

            mysqli_commit($this->conn);
            return ["status" => "success", "message" => "Chấm dứt hợp đồng thành công. Lịch sử thanh toán đã được lưu trữ."];
            
        } catch (Exception $e) {
            mysqli_rollback($this->conn);
            return ["status" => "error", "message" => "Lỗi: " . $e->getMessage()];
        }
    }

    /**
     * Xóa hợp đồng vĩnh viễn (chỉ dùng trong trường hợp đặc biệt)
     */
    public function deleteContract($contract_id) {
        $sql = "DELETE FROM Contract WHERE contract_id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $contract_id);
        
        if (mysqli_stmt_execute($stmt)) {
            return ["status" => "success", "message" => "Xóa hợp đồng thành công"];
        } else {
            return ["status" => "error", "message" => mysqli_error($this->conn)];
        }
    }

    /**
     * Lấy danh sách phòng còn trống để chuyển phòng
     */
    public function getAvailableRooms($gender = null) {
        $sql = "SELECT room_id, building, floor, capacity, available_beds, 
                       price_per_month, gender_restriction
                FROM Room 
                WHERE available_beds > 0 AND status = 'Trống'";
        
        if ($gender) {
            $sql .= " AND (gender_restriction = ? OR gender_restriction = 'Không giới hạn')";
        }
        
        $sql .= " ORDER BY building, floor, room_id";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        if ($gender) {
            mysqli_stmt_bind_param($stmt, "s", $gender);
        }
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        
        return ["status" => "success", "data" => $data];
    }

    /**
     * Thống kê hợp đồng theo trạng thái
     */
    public function getStatistics() {
        $sql = "SELECT 
                    status,
                    COUNT(*) as total
                FROM Contract
                GROUP BY status";
        
        $result = mysqli_query($this->conn, $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[$row['status']] = $row['total'];
        }
        
        return ["status" => "success", "data" => $data];
    }

    /**
     * 1. Quét và lấy danh sách sinh viên cần gửi mail (Hết hạn vào ngày mai)
     */
    // public function getContractsExpiringTomorrow() {
    //     // Lấy các hợp đồng có ngày kết thúc là NGÀY MAI và đang Hiệu lực
    //     $sql = "
    //         SELECT 
    //             c.contract_id, 
    //             c.end_date,
    //             u.email, 
    //             u.full_name, 
    //             r.room_name,
    //             rm.building
    //         FROM Contract c
    //         INNER JOIN RoomRegistration rr ON c.registration_id = rr.registration_id
    //         INNER JOIN Users u ON rr.student_id = u.user_id
    //         INNER JOIN Room r ON rr.room_id = r.room_id
    //         INNER JOIN Room rm ON rr.room_id = rm.room_id
    //         WHERE c.status = 'Hiệu lực' 
    //         AND c.end_date = DATE_ADD(CURDATE(), INTERVAL 1 DAY)
    //     ";

    //     $result = mysqli_query($this->conn, $sql);
    //     $data = [];
    //     while ($row = mysqli_fetch_assoc($result)) {
    //         $data[] = $row;
    //     }
    //     return $data;
    // }

/**
     * XỬ LÝ HỢP ĐỒNG HẾT HẠN (Tự động trả phòng & Giải phóng giường)
     * Khi chạy hàm này:
     * 1. Contract -> 'Hết hạn'
     * 2. RoomRegistration -> 'Đã trả phòng' (Sinh viên không còn trong danh sách phòng)
     * 3. Bed -> 'Trống' (Giường trống, Trigger tự cập nhật số chỗ trống của phòng)
     */
    public function processExpiredContracts() {
        // 1. Tìm các hợp đồng đang 'Hiệu lực' nhưng ngày kết thúc đã qua (nhỏ hơn ngày hiện tại)
        // Cần JOIN bảng RoomRegistration để lấy bed_id và registration_id
        $sqlFind = "
            SELECT c.contract_id, c.registration_id, rr.bed_id
            FROM Contract c
            INNER JOIN RoomRegistration rr ON c.registration_id = rr.registration_id
            WHERE c.status = 'Hiệu lực' 
            AND c.end_date <= CURDATE() 
        ";
        
        $result = mysqli_query($this->conn, $sqlFind);
        $count = 0;

        while ($row = mysqli_fetch_assoc($result)) {
            $contractId = $row['contract_id'];
            $regId = $row['registration_id'];
            $bedId = $row['bed_id'];

            mysqli_begin_transaction($this->conn);
            try {
                // Bước A: Cập nhật Hợp đồng thành 'Hết hạn'
                $upContract = "UPDATE Contract SET status = 'Hết hạn' WHERE contract_id = ?";
                $stmt1 = mysqli_prepare($this->conn, $upContract);
                mysqli_stmt_bind_param($stmt1, "i", $contractId);
                mysqli_stmt_execute($stmt1);

                // Bước B: Cập nhật Đăng ký thành 'Đã trả phòng'
                // Việc này làm sinh viên biến mất khỏi danh sách "Thành viên trong phòng"
                $upReg = "UPDATE RoomRegistration SET status = 'Đã trả phòng' WHERE registration_id = ?";
                $stmt2 = mysqli_prepare($this->conn, $upReg);
                mysqli_stmt_bind_param($stmt2, "i", $regId);
                mysqli_stmt_execute($stmt2);

                // Bước C: Giải phóng giường thành 'Trống'
                // Trigger 'update_room_status_when_full' trong DB của bạn sẽ tự động chạy để tăng available_beds
                if ($bedId) {
                    $upBed = "UPDATE Bed SET status = 'Trống' WHERE bed_id = ?";
                    $stmt3 = mysqli_prepare($this->conn, $upBed);
                    mysqli_stmt_bind_param($stmt3, "i", $bedId);
                    mysqli_stmt_execute($stmt3);
                }

                mysqli_commit($this->conn);
                $count++;
            } catch (Exception $e) {
                mysqli_rollback($this->conn);
                // Có thể ghi log lỗi ở đây nếu cần
            }
        }
        return $count; // Trả về số lượng hợp đồng đã xử lý
    }

}
?>
