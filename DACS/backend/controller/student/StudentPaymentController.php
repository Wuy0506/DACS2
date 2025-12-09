<?php
/**
 * StudentPaymentController.php
 * Controller xử lý thanh toán cho sinh viên
 */
session_start();
require_once __DIR__ . '/../../model/student/StudentPaymentModel.php';

class StudentPaymentController {
    private $model;

    public function __construct() {
        $this->model = new StudentPaymentModel();
    }

    /**
     * Xử lý các request
     */
    public function handleRequest() {
        $action = isset($_GET['action']) ? $_GET['action'] : '';
        
        switch ($action) {
            case 'getPendingFees':
                $this->getPendingFees();
                break;
            case 'getPaymentHistory':
                $this->getPaymentHistory();
                break;
            case 'getStatistics':
                $this->getStatistics();
                break;
            case 'getContract':
                $this->getContract();
                break;
            case 'getPaymentDetail':
                $this->getPaymentDetail();
                break;
            // case 'createPayment':
            //     $this->createPayment();
                break;
            case 'getAll':
                $this->getAllPaymentInfo();
                break;
            case 'createVnpayUrl':
            // --- THÊM MỚI: Xử lý tạo link thanh toán VNPAY ---
                $this->createVnpayUrl();
                break;
            // --- THÊM MỚI: Xử lý khi VNPAY trả kết quả về ---
            case 'vnpay_return':
                $this->handleVnpayReturn();
                break;
            case 'requestCheckout':
                $this->requestCheckout();
                break;
            case 'cancelCheckout':
                $this->cancelCheckout();
                break;
            default:
                $this->sendResponse([
                    'success' => false,
                    'message' => 'Action không hợp lệ'
                ]);
                break;
        }
    }

    /**
     * Kiểm tra đăng nhập
     */
    private function checkAuth() {
        if (!isset($_SESSION['user_id'])) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Chưa đăng nhập'
            ]);
            exit();
        }
        return $_SESSION['user_id'];
    }

    /**
     * Lấy tất cả thông tin thanh toán (pending fees + history + statistics + contract)
     */
    private function getAllPaymentInfo() {
        $userId = $this->checkAuth();
        
        $contract = $this->model->getActiveContract($userId);
        $pendingFees = $this->model->getPendingFees($userId);
        $history = $this->model->getPaymentHistory($userId, 20);
        $statistics = $this->model->getPaymentStatistics($userId);

        $this->sendResponse([
            'success' => true,
            'data' => [
                'contract' => $contract['success'] ? $contract['data'] : null,
                'pending_fees' => $pendingFees['success'] ? $pendingFees['data'] : [],
                'history' => $history['success'] ? $history['data'] : [],
                'statistics' => $statistics['success'] ? $statistics['data'] : null
            ]
        ]);
    }

    /**
     * Lấy các khoản phí cần đóng
     */
    private function getPendingFees() {
        $userId = $this->checkAuth();
        $result = $this->model->getPendingFees($userId);
        $this->sendResponse($result);
    }

    /**
     * Lấy lịch sử thanh toán
     */
    private function getPaymentHistory() {
        $userId = $this->checkAuth();
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
        $result = $this->model->getPaymentHistory($userId, $limit);
        $this->sendResponse($result);
    }

    /**
     * Lấy thống kê thanh toán
     */
    private function getStatistics() {
        $userId = $this->checkAuth();
        $result = $this->model->getPaymentStatistics($userId);
        $this->sendResponse($result);
    }

    /**
     * Lấy thông tin hợp đồng
     */
    private function getContract() {
        $userId = $this->checkAuth();
        $result = $this->model->getActiveContract($userId);
        $this->sendResponse($result);
    }

    /**
     * Lấy chi tiết thanh toán
     */
    private function getPaymentDetail() {
        $userId = $this->checkAuth();
        
        if (!isset($_GET['payment_id'])) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Thiếu mã thanh toán'
            ]);
            return;
        }

        $paymentId = (int)$_GET['payment_id'];
        $result = $this->model->getPaymentDetail($paymentId, $userId);
        $this->sendResponse($result);
    }

    /**
     * Tạo thanh toán mới
     */
    // private function createPayment() {
    //     $userId = $this->checkAuth();
        
    //     // Chỉ chấp nhận POST request
    //     if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    //         $this->sendResponse([
    //             'success' => false,
    //             'message' => 'Phương thức không hợp lệ'
    //         ]);
    //         return;
    //     }

    //     // Lấy dữ liệu từ request
    //     $input = json_decode(file_get_contents('php://input'), true);
        
    //     if (!$input) {
    //         $this->sendResponse([
    //             'success' => false,
    //             'message' => 'Dữ liệu không hợp lệ'
    //         ]);
    //         return;
    //     }

    //     // Validate dữ liệu
    //     $errors = $this->validatePaymentData($input);
    //     if (!empty($errors)) {
    //         $this->sendResponse([
    //             'success' => false,
    //             'message' => implode(', ', $errors)
    //         ]);
    //         return;
    //     }

    //     // Nếu có payment_id (hóa đơn đã được staff tạo), chỉ cập nhật trạng thái
    //     if (!empty($input['payment_id'])) {
    //         $result = $this->model->markInvoiceAsPaid(
    //             $input['payment_id'],
    //             $userId,
    //             isset($input['payment_method']) ? $input['payment_method'] : 'Chuyển khoản online',
    //             isset($input['description']) ? $input['description'] : ''
    //         );
    //         $this->sendResponse($result);
    //         return;
    //     }

    //     // Kiểm tra đã thanh toán tháng này chưa (chỉ áp dụng cho thanh toán mới, không có payment_id)
    //     if (isset($input['month']) && $this->model->isMonthPaid($userId, $input['payment_type'], $input['month'])) {
    //         $this->sendResponse([
    //             'success' => false,
    //             'message' => 'Bạn đã thanh toán khoản này rồi'
    //         ]);
    //         return;
    //     }

    //     // Thêm student_id vào dữ liệu và tạo thanh toán mới (ví dụ tiền phòng)
    //     $input['student_id'] = $userId;
    //     $result = $this->model->createPayment($input);
    //     $this->sendResponse($result);
    // }

    /**
     * Validate dữ liệu thanh toán
     */
    private function validatePaymentData($data) {
        $errors = [];

        if (!isset($data['payment_type']) || empty($data['payment_type'])) {
            $errors[] = "Loại thanh toán không được để trống";
        } else {
            $validTypes = ['Phòng', 'Điện', 'Nước', 'Khác'];
            if (!in_array($data['payment_type'], $validTypes)) {
                $errors[] = "Loại thanh toán không hợp lệ";
            }
        }

        if (!isset($data['amount']) || !is_numeric($data['amount']) || $data['amount'] <= 0) {
            $errors[] = "Số tiền phải là số dương";
        }

        return $errors;
    }

    /**
     * Tạo URL thanh toán VNPAY (Phiên bản Chuẩn SDK - Fix lỗi Sai chữ ký)
     */
    private function createVnpayUrl() {
        // 1. CẤU HÌNH
        $vnp_TmnCode = "E2GQBZCH"; 
        $vnp_HashSecret = "MDHZ1419XWIG8G6BDQ4H87GJXA5MHFBP"; 
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = "http://localhost/backend/controller/student/StudentPaymentController.php?action=vnpay_return";

        date_default_timezone_set('Asia/Ho_Chi_Minh'); // Ép buộc múi giờ Việt Nam
        $startTime = date("YmdHis");
        $expire = date('YmdHis', strtotime('+15 minutes', strtotime($startTime)));

        $userId = $this->checkAuth();
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            $this->sendResponse(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
        }

        // Tạo Payment ID tạm
        $paymentId = null;
        if (!empty($input['payment_id'])) {
            $paymentId = $input['payment_id'];
        } else {
            $input['status'] = 'Chưa thanh toán'; 
            $input['student_id'] = $userId;
            $input['payment_method'] = 'VNPAY';
            $result = $this->model->createPayment($input); 
            if (!$result['success']) {
                $this->sendResponse(['success' => false, 'message' => $result['message']]);
            }
            $paymentId = $result['payment_id'];
        }

        // 2. CHUẨN BỊ DỮ LIỆU
        $vnp_TxnRef = $paymentId; 
        $vnp_OrderInfo = "Thanh toan hoa don " . $paymentId; // Nội dung ngắn gọn, không dấu
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = (int)($input['amount'] * 100); // Ép kiểu int
        $vnp_Locale = 'vn';
        $vnp_BankCode = 'NCB'; 
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
        // $vnp_IpAddr = "127.0.0.1"; // Fix cứng IP để tránh lỗi ::1 trên máy lạ

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => $startTime, // Sử dụng biến thời gian đã ép múi giờ
            "vnp_ExpireDate" => $expire,    // Thêm tham số này để tránh lỗi quá hạn
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        // 3. TẠO CHUỖI HASH VÀ QUERY STRING (LOGIC QUAN TRỌNG)
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        $this->sendResponse(['success' => true, 'payment_url' => $vnp_Url]);
    }

    
    /**
     * Xử lý kết quả trả về từ VNPAY
     */
    private function handleVnpayReturn() {
        // Khai báo Key trực tiếp tại đây để đảm bảo an toàn
        $vnp_HashSecret = "MDHZ1419XWIG8G6BDQ4H87GJXA5MHFBP"; 
        
        $inputData = array();
        foreach ($_GET as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }
        
        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHash']);
        
        // Xóa các params không liên quan của VNPAY nếu có
        unset($inputData['vnp_SecureHashType']);
        unset($inputData['action']); // Xóa tham số action của MVC mình

        ksort($inputData);
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        
        // Đường dẫn frontend (Sửa lại cho đúng thư mục của bạn nếu cần)
        $frontendUrl = "/frontend/frontend-html/customer/roomOfStudent.php"; 

        if ($secureHash == $vnp_SecureHash) {
            if ($_GET['vnp_ResponseCode'] == '00') {
                $paymentId = $_GET['vnp_TxnRef'];
                $userId = $this->checkAuth(); 
                
                // Cập nhật database
                $this->model->markInvoiceAsPaid($paymentId, $userId, 'VNPAY', '');

                header("Location: $frontendUrl?payment_status=success");
            } else {
                header("Location: $frontendUrl?payment_status=failed");
            }
        } else {
            echo "Chu ky khong hop le"; // Debug: Nếu vẫn lỗi thì sẽ hiện dòng này
        }
        exit();
    }

    /**
     * Xử lý yêu cầu trả phòng
     */
    private function requestCheckout() {
        $userId = $this->checkAuth();

        // Chỉ chấp nhận POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendResponse(['success' => false, 'message' => 'Phương thức không hợp lệ']);
        }

        // 1. Kiểm tra công nợ
        $hasDebt = $this->model->checkHasUnpaidBills($userId);
        
        if ($hasDebt) {
            $this->sendResponse([
                'success' => false, 
                'message' => 'Bạn còn các khoản phí chưa thanh toán (Tiền phòng/Điện/Nước). Vui lòng thanh toán hết trước khi trả phòng.'
            ]);
        }

        // 2. Nếu không nợ, tiến hành gửi yêu cầu
        $result = $this->model->requestContractTermination($userId);
        $this->sendResponse($result);
    }

    private function cancelCheckout() {
        $userId = $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendResponse(['success' => false, 'message' => 'Phương thức không hợp lệ']);
        }
        
        $result = $this->model->cancelCheckoutRequest($userId);
        $this->sendResponse($result);
    }


    /**
     * Gửi response JSON
     */
    private function sendResponse($data) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit();
    }
}

// Khởi tạo controller
$controller = new StudentPaymentController();
$controller->handleRequest();
?>
