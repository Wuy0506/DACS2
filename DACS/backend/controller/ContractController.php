    <?php
    require_once __DIR__ . '/../model/staff/ContractModel.php';
    require_once __DIR__ . '/../service/EmailService.php';
    require_once __DIR__ . '/../helper/ContractPDFGenerator.php';

    class ContractController {
        private $model;

        public function __construct() {
            $this->model = new ContractModel();
        }

        /**
         * Lấy danh sách tất cả hợp đồng
         */
        public function getAllContracts() {
            return $this->model->getAllContracts();
        }

        /**
         * Lấy thông tin chi tiết 1 hợp đồng
         */
        public function getContractById($contract_id) {
            if (!$contract_id || !is_numeric($contract_id)) {
                return ["status" => "error", "message" => "ID hợp đồng không hợp lệ"];
            }
            return $this->model->getContractById($contract_id);
        }

        /**
         * Cập nhật/gia hạn hợp đồng
         */
        public function updateContract($data) {
            // Validate dữ liệu
            if (!isset($data['contract_id']) || !is_numeric($data['contract_id'])) {
                return ["status" => "error", "message" => "ID hợp đồng không hợp lệ"];
            }

            if (!isset($data['end_date']) || empty($data['end_date'])) {
                return ["status" => "error", "message" => "Ngày kết thúc không được để trống"];
            }

            // Kiểm tra ngày kết thúc phải sau ngày hiện tại
            $end_date = strtotime($data['end_date']);
            if ($end_date < time()) {
                return ["status" => "error", "message" => "Ngày kết thúc phải sau ngày hiện tại"];
            }

            if (!isset($data['status']) || empty($data['status'])) {
                $data['status'] = 'Hiệu lực';
            }

            return $this->model->updateContract($data);
        }

        /**
         * Gia hạn hợp đồng và tạo hóa đơn tiền phòng
         */
        public function extendContract($contract_id, $new_end_date, $extend_months = null) {
            if (!$contract_id || !is_numeric($contract_id)) {
                return ["status" => "error", "message" => "ID hợp đồng không hợp lệ"];
            }

            if (empty($new_end_date)) {
                return ["status" => "error", "message" => "Ngày hết hạn mới không được để trống"];
            }

            // Lấy thông tin hợp đồng hiện tại
            $contract = $this->model->getContractById($contract_id);
            if ($contract['status'] !== 'success') {
                return ["status" => "error", "message" => "Không tìm thấy hợp đồng"];
            }

            $contractData = $contract['data'];

            // Chuẩn hóa thời gian để so sánh theo ngày (mục đích tính số tháng)
            $currentEnd = (new DateTime($contractData['end_date']))->setTime(0, 0, 0);
            $newEnd = (new DateTime($new_end_date))->setTime(0, 0, 0);

            // Tính số tháng gia hạn
            if ($extend_months === null || $extend_months <= 0) {
                $interval = $currentEnd->diff($newEnd);
                $extend_months = ($interval->y * 12) + $interval->m;
                if ($interval->d > 0) {
                    $extend_months++; // Làm tròn lên nếu có ngày lẻ
                }
            }

            // Đảm bảo tối thiểu 1 tháng gia hạn
            if ($extend_months <= 0) {
                $extend_months = 1;
            }

            // Cập nhật ngày hết hạn
            $data = [
                'contract_id' => $contract_id,
                'end_date' => $new_end_date,
                'status' => 'Hiệu lực'
            ];

            $updateResult = $this->model->updateContract($data);
            
            if ($updateResult['status'] !== 'success') {
                return $updateResult;
            }

            // KHÔNG tạo hóa đơn khi gia hạn - Admin sẽ tạo thủ công khi cần
            // $invoiceResult = $this->createExtensionInvoice($contractData, $extend_months);

            // Gửi email hợp đồng sau khi gia hạn thành công
            $emailSent = false;
            $emailMessage = '';
            
            if (!empty($contractData['student_email'])) {
                try {
                    // Tạo PDF hợp đồng
                    $pdfResult = ContractPDFGenerator::generatePDF($contract_id);
                    
                    if ($pdfResult['success']) {
                        // Gửi email với PDF attachment
                        $emailResult = EmailService::sendContractEmail(
                            $contract_id,
                            $contractData['student_email'],
                            $contractData['student_name'],
                            $pdfResult['data']
                        );
                        
                        if ($emailResult['success']) {
                            $emailSent = true;
                            $emailMessage = ' và đã gửi hợp đồng đến email ' . $contractData['student_email'];
                        } else {
                            $emailMessage = ' (Lỗi gửi email: ' . $emailResult['message'] . ')';
                        }
                    } else {
                        $emailMessage = ' (Lỗi tạo PDF: ' . $pdfResult['message'] . ')';
                    }
                } catch (Exception $e) {
                    $emailMessage = ' (Lỗi: ' . $e->getMessage() . ')';
                }
            }

            return [
                "status" => "success",
                "message" => "Gia hạn hợp đồng thành công" . $emailMessage,
                "email_sent" => $emailSent
            ];
        }

        /**
         * Tạo hóa đơn tiền phòng khi gia hạn hợp đồng
         */
        private function createExtensionInvoice($contractData, $months) {
            global $conn;
            
            $studentId = $contractData['user_id']; // user_id của sinh viên
            $contractId = $contractData['contract_id'];
            $pricePerMonth = $contractData['price_per_month'];
            $totalAmount = $pricePerMonth * $months;
            
            // Tạo mô tả
            $description = "Tiền phòng gia hạn $months tháng (từ " . date('d/m/Y', strtotime($contractData['end_date'])) . ")";
            
            // Kiểm tra duplicate: Có hóa đơn gia hạn tương tự trong vòng 1 phút gần đây không?
            $check_sql = "SELECT COUNT(*) as cnt FROM Payment 
                        WHERE student_id = ? 
                        AND contract_id = ? 
                        AND payment_type = 'Phòng'
                        AND amount = ?
                        AND description LIKE '%gia hạn%'
                        AND payment_date >= DATE_SUB(NOW(), INTERVAL 1 MINUTE)";
            
            $check_stmt = mysqli_prepare($conn, $check_sql);
            if ($check_stmt) {
                mysqli_stmt_bind_param($check_stmt, "iid", $studentId, $contractId, $totalAmount);
                mysqli_stmt_execute($check_stmt);
                $check_result = mysqli_stmt_get_result($check_stmt);
                $row = mysqli_fetch_assoc($check_result);
                
                if ($row['cnt'] > 0) {
                    return [
                        "success" => false, 
                        "message" => "Hóa đơn gia hạn đã được tạo trước đó. Vui lòng kiểm tra lại danh sách thanh toán."
                    ];
                }
                mysqli_stmt_close($check_stmt);
            }
            
            // Tạo hóa đơn trong bảng Payment
            $sql = "INSERT INTO Payment (student_id, contract_id, payment_type, amount, payment_date, payment_method, description, status)
                    VALUES (?, ?, 'Phòng', ?, NOW(), 'Chuyển khoản online', ?, 'Chưa thanh toán')";
            
            $stmt = mysqli_prepare($conn, $sql);
            if (!$stmt) {
                return ["success" => false, "message" => "Lỗi prepare: " . mysqli_error($conn)];
            }
            
            mysqli_stmt_bind_param($stmt, "iids", $studentId, $contractId, $totalAmount, $description);
            
            if (mysqli_stmt_execute($stmt)) {
                $paymentId = mysqli_insert_id($conn);
                return [
                    "success" => true,
                    "payment_id" => $paymentId,
                    "amount" => $totalAmount,
                    "months" => $months,
                    "message" => "Đã tạo hóa đơn tiền phòng: " . number_format($totalAmount) . "đ cho $months tháng"
                ];
            }
            
            return ["success" => false, "message" => "Lỗi tạo hóa đơn: " . mysqli_stmt_error($stmt)];
        }

        /**
         * Chuyển phòng cho sinh viên
         */
        public function changeRoom($data) {
            // Validate dữ liệu
            if (!isset($data['contract_id']) || !is_numeric($data['contract_id'])) {
                return ["status" => "error", "message" => "ID hợp đồng không hợp lệ"];
            }

            if (!isset($data['new_room_id']) || !is_numeric($data['new_room_id'])) {
                return ["status" => "error", "message" => "ID phòng mới không hợp lệ"];
            }

            if (!isset($data['start_date']) || empty($data['start_date'])) {
                return ["status" => "error", "message" => "Ngày bắt đầu không được để trống"];
            }

            if (!isset($data['end_date']) || empty($data['end_date'])) {
                return ["status" => "error", "message" => "Ngày kết thúc không được để trống"];
            }

            // Kiểm tra logic ngày tháng
            $start_date = strtotime($data['start_date']);
            $end_date = strtotime($data['end_date']);

            if ($end_date <= $start_date) {
                return ["status" => "error", "message" => "Ngày kết thúc phải sau ngày bắt đầu"];
            }

            return $this->model->changeRoom($data);
        }

        /**
         * Chấm dứt hợp đồng
         */
        public function terminateContract($contract_id, $reason = '') {
            if (!$contract_id || !is_numeric($contract_id)) {
                return ["status" => "error", "message" => "ID hợp đồng không hợp lệ"];
            }

            return $this->model->terminateContract($contract_id, $reason);
        }

        /**
         * Xóa hợp đồng vĩnh viễn
         */
        public function deleteContract($contract_id) {
            if (!$contract_id || !is_numeric($contract_id)) {
                return ["status" => "error", "message" => "ID hợp đồng không hợp lệ"];
            }

            // Kiểm tra trạng thái hợp đồng trước khi xóa
            $contract = $this->model->getContractById($contract_id);
            if ($contract['status'] === 'success' && $contract['data']['status'] === 'Hiệu lực') {
                return ["status" => "error", "message" => "Không thể xóa hợp đồng đang hiệu lực. Vui lòng chấm dứt hợp đồng trước."];
            }

            return $this->model->deleteContract($contract_id);
        }

        /**
         * Lấy danh sách phòng còn trống
         */
        public function getAvailableRooms($gender = null) {
            return $this->model->getAvailableRooms($gender);
        }

        /**
         * Lấy thống kê hợp đồng
         */
        public function getStatistics() {
            return $this->model->getStatistics();
        }
    }
    ?>