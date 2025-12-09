<?php 
include_once __DIR__ . '/../database.php';

class PaymentModel {
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    /**
     * Lấy danh sách tất cả thanh toán với thông tin chi tiết
     */
    public function getAllPayments($filters = []) {
        $sql = "
            SELECT 
                p.payment_id,
                p.student_id,
                p.contract_id,
                p.payment_type,
                p.amount,
                p.payment_date,
                p.payment_method,
                p.description,
                p.status,
                u.full_name as student_name,
                u.email as student_email,
                u.phone as student_phone,
                s.student_id as student_code,
                s.faculty,
                s.major,
                c.status as contract_status,
                rr.room_id,
                rm.room_name,
                rm.building,
                rm.floor,
                rm.price_per_month
            FROM Payment p
            LEFT JOIN Users u ON p.student_id = u.user_id
            LEFT JOIN Student s ON p.student_id = s.user_id
            LEFT JOIN Contract c ON p.contract_id = c.contract_id
            LEFT JOIN RoomRegistration rr ON c.registration_id = rr.registration_id
            LEFT JOIN Room rm ON rr.room_id = rm.room_id
            WHERE 1=1
        ";
        
        // Áp dụng bộ lọc
        if (!empty($filters['payment_type'])) {
            $type = mysqli_real_escape_string($this->conn, $filters['payment_type']);
            $sql .= " AND p.payment_type = '$type'";
        }
        
        if (!empty($filters['student_id'])) {
            $student_id = (int)$filters['student_id'];
            $sql .= " AND p.student_id = $student_id";
        }
        
        if (!empty($filters['from_date'])) {
            $from_date = mysqli_real_escape_string($this->conn, $filters['from_date']);
            $sql .= " AND DATE(p.payment_date) >= '$from_date'";
        }
        
        if (!empty($filters['to_date'])) {
            $to_date = mysqli_real_escape_string($this->conn, $filters['to_date']);
            $sql .= " AND DATE(p.payment_date) <= '$to_date'";
        }
        
        if (!empty($filters['search'])) {
            $search = mysqli_real_escape_string($this->conn, $filters['search']);
            $sql .= " AND (u.full_name LIKE '%$search%' OR s.student_id LIKE '%$search%' OR p.description LIKE '%$search%')";
        }
        
        $sql .= " ORDER BY p.payment_date DESC";
        
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
     * Lấy thông tin chi tiết 1 thanh toán theo ID
     */
    public function getPaymentById($payment_id) {
        $payment_id = (int)$payment_id;
        
        $sql = "
            SELECT 
                p.payment_id,
                p.student_id,
                p.contract_id,
                p.payment_type,
                p.amount,
                p.payment_date,
                p.payment_method,
                p.description,
                p.status,
                u.full_name as student_name,
                u.email as student_email,
                u.phone as student_phone,
                s.student_id as student_code,
                s.faculty,
                s.major,
                s.gender,
                s.date_of_birth,
                c.created_date as contract_date,
                c.end_date as contract_end_date,
                c.status as contract_status,
                rr.room_id,
                rr.status as registration_status,
                rm.room_name,
                rm.building,
                rm.floor,
                rm.price_per_month
            FROM Payment p
            LEFT JOIN Users u ON p.student_id = u.user_id
            LEFT JOIN Student s ON p.student_id = s.user_id
            LEFT JOIN Contract c ON p.contract_id = c.contract_id
            LEFT JOIN RoomRegistration rr ON c.registration_id = rr.registration_id
            LEFT JOIN Room rm ON rr.room_id = rm.room_id
            WHERE p.payment_id = $payment_id
        ";
        
        $result = mysqli_query($this->conn, $sql);
        
        if (!$result) {
            return ["status" => "error", "message" => mysqli_error($this->conn)];
        }

        $data = mysqli_fetch_assoc($result);
        
        if (!$data) {
            return ["status" => "error", "message" => "Không tìm thấy thanh toán"];
        }

        return ["status" => "success", "data" => $data];
    }

    /**
     * Thêm thanh toán mới (tạo hóa đơn)
     */
    public function createPayment($data) {
        $student_id = (int)$data['student_id'];
        $contract_id = isset($data['contract_id']) && !empty($data['contract_id']) ? (int)$data['contract_id'] : 'NULL';
        $payment_type = mysqli_real_escape_string($this->conn, $data['payment_type']);
        $amount = (float)$data['amount'];
        $payment_date = mysqli_real_escape_string($this->conn, $data['payment_date']);
        $payment_method = isset($data['payment_method']) ? mysqli_real_escape_string($this->conn, $data['payment_method']) : 'Chuyển khoản online';
        $description = isset($data['description']) ? mysqli_real_escape_string($this->conn, $data['description']) : '';
        $status = isset($data['status']) ? mysqli_real_escape_string($this->conn, $data['status']) : 'Chưa thanh toán';

        $sql = "
            INSERT INTO Payment (student_id, contract_id, payment_type, amount, payment_date, payment_method, description, status)
            VALUES ($student_id, $contract_id, '$payment_type', $amount, '$payment_date', '$payment_method', '$description', '$status')
        ";
        
        $result = mysqli_query($this->conn, $sql);
        
        if (!$result) {
            return ["status" => "error", "message" => mysqli_error($this->conn)];
        }

        $payment_id = mysqli_insert_id($this->conn);

        return ["status" => "success", "message" => "Tạo thanh toán thành công", "payment_id" => $payment_id];
    }

    /**
     * Cập nhật thanh toán
     */
    public function updatePayment($data) {
        $payment_id = (int)$data['payment_id'];
        $student_id = (int)$data['student_id'];
        $contract_id = isset($data['contract_id']) && !empty($data['contract_id']) ? (int)$data['contract_id'] : 'NULL';
        $payment_type = mysqli_real_escape_string($this->conn, $data['payment_type']);
        $amount = (float)$data['amount'];
        $payment_date = mysqli_real_escape_string($this->conn, $data['payment_date']);
        $payment_method = isset($data['payment_method']) ? mysqli_real_escape_string($this->conn, $data['payment_method']) : 'Chuyển khoản online';
        $description = isset($data['description']) ? mysqli_real_escape_string($this->conn, $data['description']) : '';

        $sql = "
            UPDATE Payment 
            SET student_id = $student_id,
                contract_id = $contract_id,
                payment_type = '$payment_type',
                amount = $amount,
                payment_date = '$payment_date',
                payment_method = '$payment_method',
                description = '$description'
            WHERE payment_id = $payment_id
        ";
        
        $result = mysqli_query($this->conn, $sql);
        
        if (!$result) {
            return ["status" => "error", "message" => mysqli_error($this->conn)];
        }

        return ["status" => "success", "message" => "Cập nhật thanh toán thành công"];
    }

    /**
     * Xóa thanh toán
     */
    public function deletePayment($payment_id) {
        $payment_id = (int)$payment_id;

        $sql = "DELETE FROM Payment WHERE payment_id = $payment_id";
        
        $result = mysqli_query($this->conn, $sql);
        
        if (!$result) {
            return ["status" => "error", "message" => mysqli_error($this->conn)];
        }

        return ["status" => "success", "message" => "Xóa thanh toán thành công"];
    }

    /**
     * Đánh dấu thanh toán đã hoàn thành (chuyển status sang 'Đã thanh toán')
     */
    public function markAsPaid($payment_id) {
        $payment_id = (int)$payment_id;

        // Kiểm tra payment tồn tại
        $check_sql = "SELECT payment_id, status FROM Payment WHERE payment_id = $payment_id";
        $check_result = mysqli_query($this->conn, $check_sql);
        
        if (!$check_result || mysqli_num_rows($check_result) == 0) {
            return ["status" => "error", "message" => "Không tìm thấy hóa đơn"];
        }

        $payment = mysqli_fetch_assoc($check_result);
        
        if ($payment['status'] === 'Đã thanh toán') {
            return ["status" => "error", "message" => "Hóa đơn này đã được thanh toán trước đó"];
        }

        $sql = "UPDATE Payment SET status = 'Đã thanh toán' WHERE payment_id = $payment_id";
        
        $result = mysqli_query($this->conn, $sql);
        
        if (!$result) {
            return ["status" => "error", "message" => mysqli_error($this->conn)];
        }

        return ["status" => "success", "message" => "Đã xác nhận thanh toán thành công"];
    }

    /**
     * Cập nhật trạng thái thanh toán
     */
    public function updatePaymentStatus($payment_id, $status) {
        $payment_id = (int)$payment_id;
        $valid_statuses = ['Chưa thanh toán', 'Đã thanh toán', 'Quá hạn'];
        
        if (!in_array($status, $valid_statuses)) {
            return ["status" => "error", "message" => "Trạng thái không hợp lệ"];
        }

        $status = mysqli_real_escape_string($this->conn, $status);
        $sql = "UPDATE Payment SET status = '$status' WHERE payment_id = $payment_id";
        
        $result = mysqli_query($this->conn, $sql);
        
        if (!$result) {
            return ["status" => "error", "message" => mysqli_error($this->conn)];
        }

        return ["status" => "success", "message" => "Cập nhật trạng thái thành công"];
    }

    /**
     * Lấy thống kê thanh toán
     */
    public function getStatistics($year = null, $month = null) {
        $year = $year ?: date('Y');
        $month_condition = $month ? "AND MONTH(payment_date) = $month" : "";
        
        // Tổng thu theo loại
        $sql_by_type = "
            SELECT 
                payment_type,
                COUNT(*) as count,
                SUM(amount) as total
            FROM Payment
            WHERE YEAR(payment_date) = $year $month_condition
            GROUP BY payment_type
        ";
        
        $result_by_type = mysqli_query($this->conn, $sql_by_type);
        $by_type = [];
        while ($row = mysqli_fetch_assoc($result_by_type)) {
            $by_type[$row['payment_type']] = $row;
        }
        
        // Tổng thu theo tháng trong năm
        $sql_by_month = "
            SELECT 
                MONTH(payment_date) as month,
                COUNT(*) as count,
                SUM(amount) as total
            FROM Payment
            WHERE YEAR(payment_date) = $year
            GROUP BY MONTH(payment_date)
            ORDER BY month
        ";
        
        $result_by_month = mysqli_query($this->conn, $sql_by_month);
        $by_month = [];
        while ($row = mysqli_fetch_assoc($result_by_month)) {
            $by_month[$row['month']] = $row;
        }
        
        // Tổng thu tổng cộng
        $sql_total = "
            SELECT 
                COUNT(*) as total_count,
                SUM(amount) as total_amount
            FROM Payment
            WHERE YEAR(payment_date) = $year $month_condition
        ";
        
        $result_total = mysqli_query($this->conn, $sql_total);
        $total = mysqli_fetch_assoc($result_total);
        
        return [
            "status" => "success",
            "data" => [
                "by_type" => $by_type,
                "by_month" => $by_month,
                "total" => $total,
                "year" => $year,
                "month" => $month
            ]
        ];
    }

    /**
     * Kiểm tra đã tồn tại hóa đơn theo tháng cho một sinh viên và loại thanh toán chưa
     */
    public function isMonthlyInvoiceExists($student_id, $payment_type, $month, $year) {
        $student_id = (int)$student_id;
        $payment_type = mysqli_real_escape_string($this->conn, $payment_type);
        $month = (int)$month;
        $year = (int)$year;

        $sql = "
            SELECT COUNT(*) as cnt
            FROM Payment
            WHERE student_id = $student_id
              AND payment_type = '$payment_type'
              AND MONTH(payment_date) = $month
              AND YEAR(payment_date) = $year
        ";

        $result = mysqli_query($this->conn, $sql);
        if (!$result) {
            return false;
        }

        $row = mysqli_fetch_assoc($result);
        return isset($row['cnt']) && $row['cnt'] > 0;
    }

    /**
     * Lấy danh sách sinh viên đang ở ký túc xá
     */
    public function getActiveStudents() {
        $sql = "
            SELECT DISTINCT
                s.user_id as student_id,
                u.full_name as student_name,
                s.student_id as student_code,
                s.faculty,
                s.major,
                u.email,
                u.phone
            FROM Student s
            INNER JOIN Users u ON s.user_id = u.user_id
            INNER JOIN RoomRegistration rr ON s.user_id = rr.student_id
            INNER JOIN Contract c ON rr.registration_id = c.registration_id
            WHERE c.status = 'Hiệu lực'
            AND c.end_date >= CURDATE()
            ORDER BY u.full_name
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
     * Lấy hợp đồng hiện tại của sinh viên
     */
    public function getStudentActiveContract($student_id) {
        $student_id = (int)$student_id;
        
        $sql = "
            SELECT 
                c.contract_id,
                c.created_date,
                c.end_date,
                c.status,
                rr.room_id,
                rm.building,
                rm.floor,
                rm.price_per_month
            FROM Contract c
            INNER JOIN RoomRegistration rr ON c.registration_id = rr.registration_id
            INNER JOIN Room rm ON rr.room_id = rm.room_id
            WHERE rr.student_id = $student_id
            AND c.status = 'Hiệu lực'
            AND c.end_date >= CURDATE()
            ORDER BY c.created_date DESC
            LIMIT 1
        ";
        
        $result = mysqli_query($this->conn, $sql);
        
        if (!$result) {
            return ["status" => "error", "message" => mysqli_error($this->conn)];
        }

        $data = mysqli_fetch_assoc($result);
        
        if (!$data) {
            return ["status" => "error", "message" => "Sinh viên không có hợp đồng hiệu lực"];
        }

        return ["status" => "success", "data" => $data];
    }

    /**
     * Lấy lịch sử thanh toán của sinh viên
     */
    public function getStudentPaymentHistory($student_id) {
        $student_id = (int)$student_id;
        
        $sql = "
            SELECT 
                p.payment_id,
                p.payment_type,
                p.amount,
                p.payment_date,
                p.payment_method,
                p.description
            FROM Payment p
            WHERE p.student_id = $student_id
            ORDER BY p.payment_date DESC
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
     * Lấy tổng số tiền sinh viên đã thanh toán
     */
    public function getStudentTotalPayment($student_id, $payment_type = null) {
        $student_id = (int)$student_id;
        
        $sql = "
            SELECT 
                COALESCE(SUM(amount), 0) as total
            FROM Payment
            WHERE student_id = $student_id
        ";
        
        if ($payment_type) {
            $payment_type = mysqli_real_escape_string($this->conn, $payment_type);
            $sql .= " AND payment_type = '$payment_type'";
        }
        
        $result = mysqli_query($this->conn, $sql);
        
        if (!$result) {
            return ["status" => "error", "message" => mysqli_error($this->conn)];
        }

        $data = mysqli_fetch_assoc($result);

        return ["status" => "success", "data" => $data];
    }

    /**
     * Lấy giá điện/nước từ SystemSettings
     * setting_id = 1: Giá điện (electric_price_per_kwh)
     * setting_id = 2: Giá nước (water_price_per_m3)
     */
    public function getUtilityPrices() {
        $sql = "SELECT setting_id, setting_name, setting_value FROM SystemSettings WHERE setting_id IN (1, 2)";
        $result = mysqli_query($this->conn, $sql);

        if (!$result) {
            return ["status" => "error", "message" => mysqli_error($this->conn)];
        }

        $data = [
            'electric_price' => 3500,  // Giá mặc định
            'water_price' => 25000     // Giá mặc định
        ];

        while ($row = mysqli_fetch_assoc($result)) {
            if ($row['setting_id'] == 1 || strpos($row['setting_name'], 'electric') !== false) {
                $data['electric_price'] = (float)$row['setting_value'];
            }
            if ($row['setting_id'] == 2 || strpos($row['setting_name'], 'water') !== false) {
                $data['water_price'] = (float)$row['setting_value'];
            }
        }

        return [
            "status" => "success",
            "data" => $data
        ];
    }

    /**
     * Lấy danh sách phòng có sinh viên đang ở trong tháng/năm
     */
    public function getOccupiedRooms($month, $year) {
        // Yêu cầu mới: lấy toàn bộ phòng có sinh viên đang ở (status = 'Đã duyệt'),
        // bỏ qua logic lọc theo tháng/năm.
        $sql = "
            SELECT DISTINCT
                r.room_id,
                r.building,
                r.floor,
                r.room_name,
                r.capacity
            FROM Room r
            INNER JOIN RoomRegistration rr ON r.room_id = rr.room_id
            WHERE rr.status = 'Đã duyệt'
            ORDER BY r.building, r.floor, r.room_name
        ";

        $result = mysqli_query($this->conn, $sql);

        if (!$result) {
            return ["status" => "error", "message" => mysqli_error($this->conn)];
        }

        $rooms = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $row['students'] = [];
            $rooms[$row['room_id']] = $row;
        }

        if (empty($rooms)) {
            return ["status" => "success", "data" => []];
        }

        // Lấy danh sách sinh viên trong các phòng
        $roomIds = implode(',', array_keys($rooms));
        $sql_students = "
            SELECT 
                rr.room_id,
                rr.student_id,
                COALESCE(u.full_name, CONCAT('Sinh viên #', rr.student_id)) as student_name,
                s.student_id as student_code,
                c.contract_id
            FROM RoomRegistration rr
            LEFT JOIN Users u ON rr.student_id = u.user_id
            LEFT JOIN Student s ON rr.student_id = s.user_id
            LEFT JOIN Contract c ON rr.registration_id = c.registration_id AND c.status = 'Hiệu lực'
            WHERE rr.room_id IN ($roomIds)
                AND rr.status = 'Đã duyệt'
        ";

        $result_students = mysqli_query($this->conn, $sql_students);

        if ($result_students) {
            while ($student = mysqli_fetch_assoc($result_students)) {
                $roomId = $student['room_id'];
                if (isset($rooms[$roomId])) {
                    $rooms[$roomId]['students'][] = [
                        'student_id' => $student['student_id'],
                        'student_name' => $student['student_name'],
                        'student_code' => $student['student_code'],
                        'contract_id' => $student['contract_id']
                    ];
                }
            }
        }

        // Chỉ trả về các phòng có sinh viên
        $data = array_filter(array_values($rooms), function($room) {
            return !empty($room['students']);
        });

        return ["status" => "success", "data" => array_values($data)];
    }
}
?>
