    <?php
    /**
     * StudentPaymentModel.php
     * Model xử lý thanh toán cho sinh viên
     * Đã sửa logic đồng bộ thanh toán Điện/Nước
     */
    include_once __DIR__ . '/../database.php';

    class StudentPaymentModel {
        private $conn;

        public function __construct() {
            global $conn;
            $this->conn = $conn;
        }

        /** * 1. Sửa hàm getActiveContract 
     * Cho phép lấy cả hợp đồng 'Hiệu lực' VÀ 'Chờ duyệt trả phòng'
     */
    public function getActiveContract($userId) {
        $sql = "
            SELECT 
                c.contract_id,
                c.created_date,
                c.end_date,
                c.status,
                rr.room_id,
                rr.start_date,
                rr.end_date as registration_end_date,
                rm.building,
                rm.floor,
                rm.room_name,
                rm.price_per_month
            FROM Contract c
            INNER JOIN RoomRegistration rr ON c.registration_id = rr.registration_id
            INNER JOIN Room rm ON rr.room_id = rm.room_id
            WHERE rr.student_id = ?
            AND c.status IN ('Hiệu lực', 'Chờ duyệt trả phòng') -- <--- SỬA DÒNG NÀY
            ORDER BY c.created_date DESC
            LIMIT 1
        ";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return ["success" => false, "message" => "Lỗi prepare: " . $this->conn->error];
        }

        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();

        if (!$data) {
            return ["success" => false, "message" => "Không tìm thấy hợp đồng hiệu lực"];
        }

        return ["success" => true, "data" => $data];
    }

        /**
         * Lấy danh sách các khoản phí cần đóng (chưa thanh toán)
         */
        /**
         * Lấy danh sách các khoản phí cần đóng (chưa thanh toán)
         * Đã sửa: Loại bỏ bộ lọc PHP, tin tưởng vào trạng thái Database để hiển thị mọi hóa đơn chưa đóng
         */
        public function getPendingFees($userId) {
            $contractResult = $this->getActiveContract($userId);
            $contractData = $contractResult['success'] ? $contractResult['data'] : null;
            $contractId = $contractData['contract_id'] ?? null;
            
            $pendingFees = [];
            
            // Query lấy tất cả hóa đơn chưa thanh toán
            $unpaidSql = "
                SELECT 
                    payment_id,
                    payment_type,
                    amount,
                    payment_date,
                    description,
                    status
                FROM Payment
                WHERE student_id = ?
                AND (status IS NULL OR status <> 'Đã thanh toán')
                ORDER BY payment_date DESC
            ";

            $stmt = $this->conn->prepare($unpaidSql);
            if ($stmt) {
                $stmt->bind_param("i", $userId);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                    $monthKey = date('Y-m', strtotime($row['payment_date']));
                    $monthDisplay = date('m/Y', strtotime($row['payment_date']));

                    // Đã xóa bỏ đoạn kiểm tra $existingMonths[...] ở đây
                    // Để đảm bảo mọi hóa đơn trạng thái "Chưa thanh toán" đều hiện ra

                    $pendingFees[] = [
                        'fee_type' => $row['payment_type'],
                        'month' => $monthKey,
                        'month_display' => 'Tháng ' . $monthDisplay,
                        'amount' => (float)$row['amount'],
                        'description' => $row['description'] ?: ($row['payment_type'] . ' tháng ' . $monthDisplay),
                        'status' => $row['status'] ?: 'Chưa thanh toán',
                        'payment_id' => (int)$row['payment_id'],
                        'contract_id' => $contractId
                    ];
                }
            }

            return [
                "success" => true,
                "data" => $pendingFees,
                "contract" => $contractData
            ];
        }

        /**
         * Helper: Lấy các tháng đã thanh toán
         */
        private function getPaidMonths($userId) {
            $sql = "
                SELECT 
                    payment_type,
                    DATE_FORMAT(payment_date, '%Y-%m') as month_key,
                    amount
                FROM Payment
                WHERE student_id = ?
                AND status = 'Đã thanh toán'
            ";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();

            $paidMonths = [];
            while ($row = $result->fetch_assoc()) {
                $type = $row['payment_type'];
                $month = $row['month_key'];
                if (!isset($paidMonths[$type])) {
                    $paidMonths[$type] = [];
                }
                $paidMonths[$type][$month] = $row['amount'];
            }

            return $paidMonths;
        }

        /**
         * Lấy lịch sử thanh toán
         */
        // public function getPaymentHistory($userId, $limit = 50) {
        //     $sql = "
        //         SELECT 
        //             p.payment_id,
        //             p.payment_type,
        //             p.amount,
        //             p.payment_date,
        //             p.payment_method,
        //             p.description,
        //             c.contract_id,
        //             rm.building,
        //             rm.floor,
        //             rm.room_name
        //         FROM Payment p
        //         LEFT JOIN Contract c ON p.contract_id = c.contract_id
        //         LEFT JOIN RoomRegistration rr ON c.registration_id = rr.registration_id
        //         LEFT JOIN Room rm ON rr.room_id = rm.room_id
        //         WHERE p.student_id = ?
        //           AND p.status = 'Đã thanh toán'
        //         ORDER BY p.payment_date DESC
        //         LIMIT ?
        //     ";

        //     $stmt = $this->conn->prepare($sql);
        //     if (!$stmt) {
        //         return ["success" => false, "message" => "Lỗi prepare: " . $this->conn->error];
        //     }

        //     $stmt->bind_param("ii", $userId, $limit);
        //     $stmt->execute();
        //     $result = $stmt->get_result();

        //     $data = [];
        //     while ($row = $result->fetch_assoc()) {
        //         $data[] = $row;
        //     }

        //     return ["success" => true, "data" => $data];
        // }
/**
     * Lấy lịch sử thanh toán
     * CẬP NHẬT: Hiển thị lịch sử cho cả trạng thái 'Hiệu lực' VÀ 'Chờ duyệt trả phòng'
     */
    public function getPaymentHistory($userId, $limit = 50) {
        $sql = "
            SELECT 
                p.payment_id,
                p.payment_type,
                p.amount,
                p.payment_date,
                p.payment_method,
                p.description,
                c.contract_id,
                c.status as contract_status,
                rm.building,
                rm.floor,
                rm.room_name
            FROM Payment p
            INNER JOIN Contract c ON p.contract_id = c.contract_id
            LEFT JOIN RoomRegistration rr ON c.registration_id = rr.registration_id
            LEFT JOIN Room rm ON rr.room_id = rm.room_id
            WHERE p.student_id = ?
              AND p.status = 'Đã thanh toán'
              -- SỬA DÒNG DƯỚI ĐÂY: Cho phép hiển thị khi đang chờ duyệt trả phòng
              AND c.status IN ('Hiệu lực', 'Chờ duyệt trả phòng') 
            ORDER BY p.payment_date DESC
            LIMIT ?
        ";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return ["success" => false, "message" => "Lỗi prepare: " . $this->conn->error];
        }

        $stmt->bind_param("ii", $userId, $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        return ["success" => true, "data" => $data];
    }

        /**
         * Lấy thống kê thanh toán
         */
        // public function getPaymentStatistics($userId) {
        //     $sql = "
        //         SELECT 
        //             payment_type,
        //             COUNT(*) as total_count,
        //             SUM(amount) as total_amount
        //         FROM Payment
        //         WHERE student_id = ?
        //           AND status = 'Đã thanh toán'
        //         GROUP BY payment_type
        //     ";

        //     $stmt = $this->conn->prepare($sql);
        //     $stmt->bind_param("i", $userId);
        //     $stmt->execute();
        //     $result = $stmt->get_result();

        //     $byType = [];
        //     $totalAmount = 0;
        //     $totalCount = 0;

        //     while ($row = $result->fetch_assoc()) {
        //         $byType[$row['payment_type']] = [
        //             'count' => $row['total_count'],
        //             'amount' => $row['total_amount']
        //         ];
        //         $totalAmount += $row['total_amount'];
        //         $totalCount += $row['total_count'];
        //     }

        //     return [
        //         "success" => true,
        //         "data" => [
        //             "by_type" => $byType,
        //             "total_amount" => $totalAmount,
        //             "total_count" => $totalCount
        //         ]
        //     ];
        // }
        public function getPaymentStatistics($userId) {
            $sql = "
                SELECT 
                    p.payment_type,
                    COUNT(p.payment_id) as total_count,
                    SUM(p.amount) as total_amount
                FROM Payment p
                INNER JOIN Contract c ON p.contract_id = c.contract_id
                WHERE p.student_id = ?
                AND p.status = 'Đã thanh toán'
                AND c.status IN ('Hiệu lực', 'Chờ duyệt trả phòng') -- <--- SỬA DÒNG NÀY
                GROUP BY p.payment_type
            ";

            $stmt = $this->conn->prepare($sql);
            // ... (phần còn lại giữ nguyên như cũ)
            if (!$stmt) {
                return ["success" => false, "message" => "Lỗi prepare: " . $this->conn->error];
            }

            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();

            $byType = [];
            $totalAmount = 0;
            $totalCount = 0;

            while ($row = $result->fetch_assoc()) {
                $byType[$row['payment_type']] = [
                    'count' => $row['total_count'],
                    'amount' => $row['total_amount']
                ];
                $totalAmount += $row['total_amount'];
                $totalCount += $row['total_count'];
            }

            return [
                "success" => true,
                "data" => [
                    "by_type" => $byType,
                    "total_amount" => $totalAmount,
                    "total_count" => $totalCount
                ]
            ];
        }

        /**
     * 3. THÊM HÀM MỚI: Hủy yêu cầu trả phòng (Quay về Hiệu lực)
     */
    public function cancelCheckoutRequest($studentId) {
        // Lấy hợp đồng đang ở trạng thái 'Chờ duyệt trả phòng'
        $sqlInfo = "
            SELECT c.contract_id 
            FROM Contract c
            INNER JOIN RoomRegistration rr ON c.registration_id = rr.registration_id
            WHERE rr.student_id = ? AND c.status = 'Chờ duyệt trả phòng'
            LIMIT 1
        ";
        $stmtInfo = $this->conn->prepare($sqlInfo);
        $stmtInfo->bind_param("i", $studentId);
        $stmtInfo->execute();
        $result = $stmtInfo->get_result()->fetch_assoc();

        if (!$result) {
            return ["success" => false, "message" => "Không tìm thấy yêu cầu trả phòng nào."];
        }

        // Cập nhật lại thành 'Hiệu lực'
        $updateSql = "UPDATE Contract SET status = 'Hiệu lực' WHERE contract_id = ?";
        $updateStmt = $this->conn->prepare($updateSql);
        $updateStmt->bind_param("i", $result['contract_id']);

        if ($updateStmt->execute()) {
            return ["success" => true, "message" => "Đã hủy yêu cầu trả phòng. Hợp đồng quay lại trạng thái hiệu lực."];
        } else {
            return ["success" => false, "message" => "Lỗi hệ thống: " . $this->conn->error];
        }
    }

        /**
         * Tạo thanh toán mới (Dành cho VNPAY hoặc tạo thủ công)
         */
        public function createPayment($data) {
            $studentId = (int)$data['student_id'];
            $contractId = isset($data['contract_id']) && !empty($data['contract_id']) ? (int)$data['contract_id'] : null;
            $paymentType = $data['payment_type'];
            $amount = (float)$data['amount'];
            $paymentDate = date('Y-m-d H:i:s');
            $paymentMethod = isset($data['payment_method']) ? $data['payment_method'] : 'Chuyển khoản online';
            $description = isset($data['description']) ? $data['description'] : '';
            $status = isset($data['status']) ? $data['status'] : 'Đã thanh toán'; 
            $month = isset($data['month']) ? $data['month'] : date('Y-m');

            // Kiểm tra xem có hóa đơn chưa thanh toán tồn tại không (tránh trùng)
            $checkSql = "
                SELECT payment_id 
                FROM Payment 
                WHERE student_id = ? 
                AND payment_type = ? 
                AND DATE_FORMAT(payment_date, '%Y-%m') = ?
                AND (status IS NULL OR status <> 'Đã thanh toán')
                LIMIT 1
            ";
            $checkStmt = $this->conn->prepare($checkSql);
            if ($checkStmt) {
                $checkStmt->bind_param("iss", $studentId, $paymentType, $month);
                $checkStmt->execute();
                $checkResult = $checkStmt->get_result();
                
                if ($checkResult->num_rows > 0) {
                    // Đã có hóa đơn, cập nhật nó
                    $existingPayment = $checkResult->fetch_assoc();
                    if ($status == 'Đã thanh toán') {
                        return $this->markInvoiceAsPaid(
                            $existingPayment['payment_id'],
                            $studentId,
                            $paymentMethod,
                            $description
                        );
                    } else {
                        return [
                            "success" => true,
                            "message" => "Đã có hóa đơn chờ",
                            "payment_id" => $existingPayment['payment_id']
                        ];
                    }
                }
            }

            // Tạo mới
            if ($contractId) {
                $sql = "INSERT INTO Payment (student_id, contract_id, payment_type, amount, payment_date, payment_method, description, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("iisdssss", $studentId, $contractId, $paymentType, $amount, $paymentDate, $paymentMethod, $description, $status);
            } else {
                $sql = "INSERT INTO Payment (student_id, payment_type, amount, payment_date, payment_method, description, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("isdssss", $studentId, $paymentType, $amount, $paymentDate, $paymentMethod, $description, $status);
            }

            if (!$stmt) {
                return ["success" => false, "message" => "Lỗi prepare: " . $this->conn->error];
            }

            if ($stmt->execute()) {
                $paymentId = $this->conn->insert_id;
                
                // Nếu tạo thanh toán đã thành công ngay lập tức cho Điện/Nước (ví dụ trả tiền mặt tại quầy), cập nhật luôn cho bạn cùng phòng
                if ($status == 'Đã thanh toán' && in_array($paymentType, ['Điện', 'Nước'])) {
                    $this->markRoommatesUtilityAsPaid($studentId, $paymentType, $month, $paymentMethod);
                }

                return [
                    "success" => true,
                    "message" => "Thanh toán thành công",
                    "payment_id" => $paymentId
                ];
            } else {
                return ["success" => false, "message" => "Lỗi: " . $stmt->error];
            }
        }

        /**
         * Đánh dấu hóa đơn là Đã thanh toán
         */
        public function markInvoiceAsPaid($paymentId, $studentId, $paymentMethod = 'Chuyển khoản online', $description = '') {
            $paymentId = (int)$paymentId;
            $studentId = (int)$studentId;

            // 1. Lấy thông tin hóa đơn hiện tại để biết Loại và Tháng
            $checkSql = "SELECT payment_id, payment_type, DATE_FORMAT(payment_date, '%Y-%m') as payment_month 
                        FROM Payment 
                        WHERE payment_id = ? AND student_id = ?";
            
            $checkStmt = $this->conn->prepare($checkSql);
            if (!$checkStmt) {
                return ["success" => false, "message" => "Lỗi prepare: " . $this->conn->error];
            }
            $checkStmt->bind_param("ii", $paymentId, $studentId);
            $checkStmt->execute();
            $result = $checkStmt->get_result();

            if ($result->num_rows === 0) {
                return ["success" => false, "message" => "Không tìm thấy hóa đơn"];
            }

            $paymentInfo = $result->fetch_assoc();
            $paymentType = $paymentInfo['payment_type'];
            $paymentMonth = $paymentInfo['payment_month'];

            // 2. Cập nhật hóa đơn của chính sinh viên này
            $sql = "
                UPDATE Payment
                SET status = 'Đã thanh toán',
                    payment_method = ?,
                    description = CASE WHEN ? <> '' THEN ? ELSE description END,
                    payment_date = NOW()
                WHERE payment_id = ? AND student_id = ?
            ";

            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                return ["success" => false, "message" => "Lỗi prepare: " . $this->conn->error];
            }

            $stmt->bind_param("sssii", $paymentMethod, $description, $description, $paymentId, $studentId);

            if (!$stmt->execute()) {
                return ["success" => false, "message" => "Lỗi: " . $stmt->error];
            }

            // 3. LOGIC QUAN TRỌNG: Nếu là Điện hoặc Nước, cập nhật cho tất cả thành viên trong phòng
            if (in_array($paymentType, ['Điện', 'Nước'])) {
                $this->markRoommatesUtilityAsPaid($studentId, $paymentType, $paymentMonth, $paymentMethod);
            }

            return ["success" => true, "message" => "Đã cập nhật trạng thái hóa đơn", "payment_id" => $paymentId];
        }

        /**
         * Đánh dấu hóa đơn điện/nước của tất cả thành viên trong phòng là đã thanh toán
         * FIX: Đã sửa lại logic JOIN và WHERE cho đúng cấu trúc database
         */
        private function markRoommatesUtilityAsPaid($studentId, $paymentType, $paymentMonth, $paymentMethod) {
            // Bước 1: Tìm ID phòng của sinh viên vừa thanh toán
            // Sử dụng RoomRegistration để tìm phòng hiện tại
            $roomSql = "
                SELECT room_id 
                FROM RoomRegistration 
                WHERE student_id = ? AND status = 'Đã duyệt' 
                LIMIT 1
            ";
            $roomStmt = $this->conn->prepare($roomSql);
            if (!$roomStmt) return;
            
            $roomStmt->bind_param("i", $studentId);
            $roomStmt->execute();
            $roomResult = $roomStmt->get_result();
            
            if ($roomResult->num_rows === 0) return; // Sinh viên không có phòng
            
            $roomData = $roomResult->fetch_assoc();
            $roomId = $roomData['room_id'];

            // Bước 2: Cập nhật Payment cho TẤT CẢ sinh viên khác trong cùng phòng (Room ID)
            // Điều kiện: Cùng Payment Type, Cùng Tháng, Chưa thanh toán
            $updateSql = "
                UPDATE Payment p
                INNER JOIN RoomRegistration rr ON p.student_id = rr.student_id
                SET p.status = 'Đã thanh toán',
                    p.payment_method = ?,
                    p.payment_date = NOW(),
                    p.description = CONCAT(p.description, ' (Được thanh toán bởi bạn cùng phòng)')
                WHERE rr.room_id = ? 
                AND rr.status = 'Đã duyệt'
                AND p.payment_type = ?
                AND DATE_FORMAT(p.payment_date, '%Y-%m') = ?
                AND p.student_id <> ? 
                AND (p.status IS NULL OR p.status <> 'Đã thanh toán')
            ";
            
            $updateStmt = $this->conn->prepare($updateSql);
            if ($updateStmt) {
                // Bind params: method (s), room_id (i), type (s), month (s), current_student_id (i)
                $updateStmt->bind_param("sissi", $paymentMethod, $roomId, $paymentType, $paymentMonth, $studentId);
                $updateStmt->execute();
            }
        }

        public function getPaymentDetail($paymentId, $userId) {
            $sql = "
                SELECT 
                    p.*,
                    rm.building,
                    rm.floor,
                    rm.room_name
                FROM Payment p
                LEFT JOIN Contract c ON p.contract_id = c.contract_id
                LEFT JOIN RoomRegistration rr ON c.registration_id = rr.registration_id
                LEFT JOIN Room rm ON rr.room_id = rm.room_id
                WHERE p.payment_id = ? AND p.student_id = ?
            ";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $paymentId, $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();

            if (!$data) {
                return ["success" => false, "message" => "Không tìm thấy thông tin thanh toán"];
            }

            return ["success" => true, "data" => $data];
        }

        /**
     * Kiểm tra xem sinh viên còn nợ khoản nào không
     * Trả về true nếu còn nợ, false nếu đã sạch nợ
     */
    public function checkHasUnpaidBills($studentId) {
        // Kiểm tra trong bảng Payment các khoản chưa thanh toán
        $sql = "
            SELECT COUNT(*) as total_unpaid
            FROM Payment
            WHERE student_id = ? 
              AND (status IS NULL OR status <> 'Đã thanh toán')
        ";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $studentId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        return $result['total_unpaid'] > 0;
    }

    /**
     * Gửi yêu cầu trả phòng (Chuyển trạng thái hợp đồng sang 'Chờ duyệt trả phòng')
     */
    public function requestContractTermination($studentId) {
        // 1. Lấy hợp đồng đang hiệu lực
        $activeContract = $this->getActiveContract($studentId);
        
        if (!$activeContract['success']) {
            return ["success" => false, "message" => "Không tìm thấy hợp đồng đang hiệu lực."];
        }
        
        $contractId = $activeContract['data']['contract_id'];

        // 2. Cập nhật trạng thái
        $sql = "UPDATE Contract SET status = 'Chờ duyệt trả phòng' WHERE contract_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $contractId);
        
        if ($stmt->execute()) {
            return ["success" => true, "message" => "Đã gửi yêu cầu trả phòng. Vui lòng đợi nhân viên duyệt."];
        } else {
            return ["success" => false, "message" => "Lỗi hệ thống: " . $this->conn->error];
        }
    }

    

    }
?>