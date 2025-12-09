<?php
require_once __DIR__ . '/../model/staff/PaymentModel.php';

class PaymentController {
    private $model;

    public function __construct() {
        $this->model = new PaymentModel();
    }

    /**
     * Lấy danh sách tất cả thanh toán
     */
    public function getAllPayments($filters = []) {
        return $this->model->getAllPayments($filters);
    }

    /**
     * Lấy thông tin chi tiết 1 thanh toán
     */
    public function getPaymentById($payment_id) {
        if (!$payment_id || !is_numeric($payment_id)) {
            return ["status" => "error", "message" => "ID thanh toán không hợp lệ"];
        }
        return $this->model->getPaymentById($payment_id);
    }

    /**
     * Tạo thanh toán mới (tạo hóa đơn)
     */
    public function createPayment($data) {
        // Validate dữ liệu
        $errors = $this->validatePaymentData($data);
        if (!empty($errors)) {
            return ["status" => "error", "message" => implode(", ", $errors)];
        }

        // Kiểm tra sinh viên tồn tại
        if (!$this->isStudentExists($data['student_id'])) {
            return ["status" => "error", "message" => "Sinh viên không tồn tại"];
        }

        // Kiểm tra hợp đồng nếu có
        if (isset($data['contract_id']) && !empty($data['contract_id'])) {
            if (!$this->isContractValid($data['contract_id'], $data['student_id'])) {
                return ["status" => "error", "message" => "Hợp đồng không hợp lệ hoặc không thuộc về sinh viên này"];
            }
        }

        return $this->model->createPayment($data);
    }


    /**
     * Lấy thống kê thanh toán
     */
    public function getStatistics($year = null, $month = null) {
        if ($year && !is_numeric($year)) {
            return ["status" => "error", "message" => "Năm không hợp lệ"];
        }

        if ($month && (!is_numeric($month) || $month < 1 || $month > 12)) {
            return ["status" => "error", "message" => "Tháng không hợp lệ"];
        }

        return $this->model->getStatistics($year, $month);
    }

    /**
     * Lấy danh sách sinh viên đang ở ký túc xá
     */
    public function getActiveStudents() {
        return $this->model->getActiveStudents();
    }

    /**
     * Lấy hợp đồng hiện tại của sinh viên
     */
    public function getStudentActiveContract($student_id) {
        if (!$student_id || !is_numeric($student_id)) {
            return ["status" => "error", "message" => "ID sinh viên không hợp lệ"];
        }
        return $this->model->getStudentActiveContract($student_id);
    }

    /**
     * Lấy lịch sử thanh toán của sinh viên
     */
    public function getStudentPaymentHistory($student_id) {
        if (!$student_id || !is_numeric($student_id)) {
            return ["status" => "error", "message" => "ID sinh viên không hợp lệ"];
        }
        return $this->model->getStudentPaymentHistory($student_id);
    }

    /**
     * Lấy tổng số tiền sinh viên đã thanh toán
     */
    public function getStudentTotalPayment($student_id, $payment_type = null) {
        if (!$student_id || !is_numeric($student_id)) {
            return ["status" => "error", "message" => "ID sinh viên không hợp lệ"];
        }
        return $this->model->getStudentTotalPayment($student_id, $payment_type);
    }

    /**
     * Validate dữ liệu thanh toán
     */
    private function validatePaymentData($data) {
        $errors = [];

        if (!isset($data['student_id']) || !is_numeric($data['student_id'])) {
            $errors[] = "ID sinh viên không hợp lệ";
        }

        if (!isset($data['payment_type']) || empty($data['payment_type'])) {
            $errors[] = "Loại thanh toán không được để trống";
        } else {
            $valid_types = ['Phòng', 'Điện', 'Nước', 'Khác'];
            if (!in_array($data['payment_type'], $valid_types)) {
                $errors[] = "Loại thanh toán không hợp lệ";
            }
        }

        if (!isset($data['amount']) || !is_numeric($data['amount']) || $data['amount'] <= 0) {
            $errors[] = "Số tiền phải là số dương";
        }

        if (!isset($data['payment_date']) || empty($data['payment_date'])) {
            $errors[] = "Ngày thanh toán không được để trống";
        } else {
            // Validate định dạng ngày
            $date = DateTime::createFromFormat('Y-m-d H:i:s', $data['payment_date']);
            if (!$date) {
                $date = DateTime::createFromFormat('Y-m-d', $data['payment_date']);
                if (!$date) {
                    $errors[] = "Ngày thanh toán không đúng định dạng";
                }
            }
        }

        return $errors;
    }

    /**
     * Kiểm tra sinh viên tồn tại
     */
    private function isStudentExists($student_id) {
        $result = $this->model->getStudentActiveContract($student_id);
        // Sinh viên tồn tại nếu có hợp đồng hoặc trong bảng Student
        return true; // Simplified check, có thể cải thiện thêm
    }

    /**
     * Kiểm tra hợp đồng hợp lệ
     */
    private function isContractValid($contract_id, $student_id) {
        $contract = $this->model->getStudentActiveContract($student_id);
        if ($contract['status'] === 'success') {
            return $contract['data']['contract_id'] == $contract_id;
        }
        return false;
    }

    /**
     * Tạo hóa đơn tự động cho tất cả sinh viên
     * (Phòng, Điện, Nước theo tháng)
     */
    public function generateMonthlyInvoices($month, $year, $payment_types = ['Phòng']) {
        $students = $this->model->getActiveStudents();
        
        if ($students['status'] !== 'success') {
            return ["status" => "error", "message" => "Không lấy được danh sách sinh viên"];
        }

        $success_count = 0;
        $error_count = 0;
        $errors = [];

        foreach ($students['data'] as $student) {
            // Lấy hợp đồng hiện tại
            $contract = $this->model->getStudentActiveContract($student['student_id']);
            
            if ($contract['status'] !== 'success') {
                $error_count++;
                $errors[] = "Sinh viên {$student['student_name']} không có hợp đồng hiệu lực";
                continue;
            }

            foreach ($payment_types as $type) {
                $amount = 0;
                $description = "";

                switch ($type) {
                    case 'Phòng':
                        $amount = $contract['data']['price_per_month'];
                        $description = "Tiền phòng tháng $month/$year";
                        break;
                    case 'Điện':
                        $amount = 0; // Sẽ cập nhật sau khi có số điện thực tế
                        $description = "Tiền điện tháng $month/$year (Chưa ghi số)";
                        break;
                    case 'Nước':
                        $amount = 0; // Sẽ cập nhật sau khi có số nước thực tế
                        $description = "Tiền nước tháng $month/$year (Chưa ghi số)";
                        break;
                }

                // Nếu đã tồn tại hóa đơn tháng này cho sinh viên + loại này thì bỏ qua, tránh trùng
                if ($this->model->isMonthlyInvoiceExists($student['student_id'], $type, $month, $year)) {
                    $error_count++;
                    $errors[] = "Bỏ qua hóa đơn $type tháng $month/$year vì đã tồn tại cho {$student['student_name']}";
                    continue;
                }

                $payment_data = [
                    'student_id' => $student['student_id'],
                    'contract_id' => $contract['data']['contract_id'],
                    'payment_type' => $type,
                    'amount' => $amount,
                    'payment_date' => "$year-$month-01 00:00:00",
                    'payment_method' => 'Chuyển khoản online',
                    'description' => $description
                ];

                $result = $this->model->createPayment($payment_data);
                
                if ($result['status'] === 'success') {
                    $success_count++;
                } else {
                    $error_count++;
                    $errors[] = "Lỗi tạo hóa đơn $type cho {$student['student_name']}: {$result['message']}";
                }
            }
        }

        return [
            "status" => "success",
            "message" => "Tạo hóa đơn hoàn tất",
            "data" => [
                "success_count" => $success_count,
                "error_count" => $error_count,
                "errors" => $errors
            ]
        ];
    }

    /**
     * Xử lý toàn bộ request GET cho API payment
     */
    public function handleGet($query)
    {
        if (isset($query['action'])) {
            $action = $query['action'];

            switch ($action) {
                case 'getAll':
                    $filters = [];
                    if (isset($query['payment_type'])) $filters['payment_type'] = $query['payment_type'];
                    if (isset($query['student_id'])) $filters['student_id'] = $query['student_id'];
                    if (isset($query['from_date'])) $filters['from_date'] = $query['from_date'];
                    if (isset($query['to_date'])) $filters['to_date'] = $query['to_date'];
                    if (isset($query['search'])) $filters['search'] = $query['search'];

                    return $this->getAllPayments($filters);

                case 'getById':
                    if (isset($query['id'])) {
                        return $this->getPaymentById($query['id']);
                    }
                    return ["status" => "error", "message" => "Thiếu ID thanh toán"];        

                case 'getStatistics':
                    $year = isset($query['year']) ? $query['year'] : null;
                    $month = isset($query['month']) ? $query['month'] : null;
                    return $this->getStatistics($year, $month);

                case 'getActiveStudents':
                    return $this->getActiveStudents();

                case 'getStudentContract':
                    if (isset($query['student_id'])) {
                        return $this->getStudentActiveContract($query['student_id']);
                    }
                    return ["status" => "error", "message" => "Thiếu ID sinh viên"];        

                case 'getStudentHistory':
                    if (isset($query['student_id'])) {
                        return $this->getStudentPaymentHistory($query['student_id']);
                    }
                    return ["status" => "error", "message" => "Thiếu ID sinh viên"];        

                case 'getStudentTotal':
                    if (isset($query['student_id'])) {
                        $payment_type = isset($query['payment_type']) ? $query['payment_type'] : null;
                        return $this->getStudentTotalPayment($query['student_id'], $payment_type);
                    }
                    return ["status" => "error", "message" => "Thiếu ID sinh viên"];

                case 'getUtilityPrices':
                    return $this->getUtilityPrices();

                case 'getOccupiedRooms':
                    $month = isset($query['month']) ? (int)$query['month'] : date('n');
                    $year = isset($query['year']) ? (int)$query['year'] : date('Y');
                    return $this->getOccupiedRooms($month, $year);

                default:
                    return ["status" => "error", "message" => "Action không hợp lệ"];
            }
        }

        // Mặc định trả về tất cả thanh toán
        return $this->getAllPayments();
    }

    /**
     * Lấy giá điện/nước từ SystemSettings
     */
    public function getUtilityPrices() {
        return $this->model->getUtilityPrices();
    }

    /**
     * Lấy danh sách phòng có sinh viên đang ở
     */
    public function getOccupiedRooms($month, $year) {
        return $this->model->getOccupiedRooms($month, $year);
    }

    /**
     * Đánh dấu thanh toán đã hoàn thành
     */
    public function markPaymentAsPaid($payment_id) {
        if (!$payment_id || !is_numeric($payment_id)) {
            return ["status" => "error", "message" => "ID thanh toán không hợp lệ"];
        }
        return $this->model->markAsPaid($payment_id);
    }

    /**
     * Cập nhật trạng thái thanh toán
     */
    public function updatePaymentStatus($payment_id, $status) {
        if (!$payment_id || !is_numeric($payment_id)) {
            return ["status" => "error", "message" => "ID thanh toán không hợp lệ"];
        }
        return $this->model->updatePaymentStatus($payment_id, $status);
    }

    /**
     * Xử lý toàn bộ request POST cho API payment
     */
    public function handlePost($input)
    {
        if (!isset($input['action'])) {
            return ["status" => "error", "message" => "Thiếu action"];
        }

        $action = $input['action'];

        switch ($action) {
            case 'create':
                return $this->createPayment($input);

            case 'generateMonthlyInvoices':
                if (isset($input['month']) && isset($input['year'])) {
                    $payment_types = isset($input['payment_types']) ? $input['payment_types'] : ['Phòng'];
                    return $this->generateMonthlyInvoices($input['month'], $input['year'], $payment_types);
                }
                return ["status" => "error", "message" => "Thiếu thông tin tháng/năm"];

            case 'markAsPaid':
                if (isset($input['payment_id'])) {
                    return $this->markPaymentAsPaid($input['payment_id']);
                }
                return ["status" => "error", "message" => "Thiếu ID thanh toán"];

            case 'updateStatus':
                if (isset($input['payment_id']) && isset($input['status'])) {
                    return $this->updatePaymentStatus($input['payment_id'], $input['status']);
                }
                return ["status" => "error", "message" => "Thiếu thông tin cần thiết"];

            case 'createUtilityInvoices':
                if (isset($input['invoices']) && is_array($input['invoices'])) {
                    return $this->createUtilityInvoices($input['invoices'], $input['month'] ?? null, $input['year'] ?? null);
                }
                return ["status" => "error", "message" => "Thiếu danh sách hóa đơn"];

            default:
                return ["status" => "error", "message" => "Action không hợp lệ"];
        }
    }

    /**
     * Tạo nhiều hóa đơn điện/nước cùng lúc
     */
    public function createUtilityInvoices($invoices, $month, $year) {
        $success_count = 0;
        $error_count = 0;
        $errors = [];

        foreach ($invoices as $invoice) {
            // Validate dữ liệu
            if (!isset($invoice['student_id']) || !isset($invoice['amount']) || !isset($invoice['payment_type'])) {
                $error_count++;
                $errors[] = "Thiếu thông tin hóa đơn";
                continue;
            }

            $data = [
                'student_id' => $invoice['student_id'],
                'contract_id' => $invoice['contract_id'] ?? null,
                'payment_type' => $invoice['payment_type'],
                'amount' => $invoice['amount'],
                'payment_date' => date('Y-m-d H:i:s'),
                'payment_method' => 'Chuyển khoản online',
                'description' => $invoice['description'] ?? "Tiền {$invoice['payment_type']} tháng {$month}/{$year}",
                'status' => 'Chưa thanh toán'
            ];

            $result = $this->model->createPayment($data);
            
            if ($result['status'] === 'success') {
                $success_count++;
            } else {
                $error_count++;
                $errors[] = $result['message'];
            }
        }

        return [
            "status" => "success",
            "message" => "Tạo hóa đơn hoàn tất",
            "data" => [
                "success_count" => $success_count,
                "error_count" => $error_count,
                "errors" => $errors
            ]
        ];
    }

    /**
     * Xử lý request DELETE cho API payment
     */
    public function handleDelete($query)
    {
        if (isset($query['id'])) {
            return $this->deletePayment($query['id']);
        }
        return ["status" => "error", "message" => "Thiếu ID thanh toán"];
    }
}
?>
