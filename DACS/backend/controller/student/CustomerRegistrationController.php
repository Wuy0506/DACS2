<?php
// Tắt hiển thị lỗi để tránh HTML xuất hiện trong JSON response
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Bắt đầu session nếu chưa có
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../model/database.php';
require_once __DIR__ . '/../../model/staff/CustomerRegistrationModel.php';

/**
 * Controller xử lý đăng ký phòng của khách hàng (Customer)
 */
class CustomerRegistrationController {
    private $model;
    
    function __construct() {
        $this->model = new CustomerRegistrationModel();
    }
    
    /**
     * Điều hướng request
     */
    public function handleRequest() {
        $action = isset($_GET['action']) ? $_GET['action'] : '';
        
        switch ($action) {
            case 'get-my-registrations':
                $this->handleGetMyRegistrations();
                break;
                
            case 'get-registration-detail':
                $this->handleGetRegistrationDetail();
                break;
                
            case 'get-my-statistics':
                $this->handleGetMyStatistics();
                break;
                
            case 'get-my-payments':
                $this->handleGetMyPayments();
                break;
                
            case 'huyDangKyPhong':
                $this->huyDangKyPhong();
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
     * Lấy danh sách đăng ký của user hiện tại
     */
    private function handleGetMyRegistrations() {
        // Debug: Log session info
        error_log("=== CustomerRegistration: Get My Registrations ===");
        error_log("Session ID: " . session_id());
        error_log("Session user_id: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'NOT SET'));
        error_log("Session is_logged_in: " . (isset($_SESSION['is_logged_in']) ? $_SESSION['is_logged_in'] : 'NOT SET'));
        
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
            error_log("User not logged in - redirecting");
            $this->sendResponse([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để xem đơn đăng ký phòng',
                'redirect' => true
            ]);
            return;
        }
        
        $userId = $_SESSION['user_id'];
        error_log("Loading registrations for user_id: " . $userId);
        
        $result = $this->model->getRegistrationsByUserId($userId);
        error_log("Result: " . json_encode($result));
        
        $this->sendResponse($result);
    }
    
    /**
     * Lấy chi tiết đăng ký
     */
private function handleGetRegistrationDetail() {
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user_id'])) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Vui lòng đăng nhập'
            ]);
            return;
        }
        
        $userId = $_SESSION['user_id'];
        $registrationId = isset($_GET['registration_id']) ? intval($_GET['registration_id']) : 0;
        
        if (empty($registrationId)) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Thiếu registration_id'
            ]);
            return;
        }
        
        $result = $this->model->getRegistrationDetail($registrationId, $userId);
        $this->sendResponse($result);
    }

    // huỷ đăng ký phòng
    private function huyDangKyPhong(){
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user_id'])) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Vui lòng đăng nhập'
            ]);
            return;
        }

        if(!isset($_GET['registration_id'])){
            $this->sendResponse([
                'success' =>false,
                'message' => 'Thiếu registration_id'
            ]);
            return;
        }

        $userId = $_SESSION['user_id'];
        $registrationId = intval($_GET['registration_id']);

        $result = $this->model->huyDangKyPhong($registrationId,$userId);
        $this->sendResponse($result);
    }
    
    /**
     * Lấy thống kê của user
     */
    private function handleGetMyStatistics() {
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user_id'])) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Vui lòng đăng nhập'
            ]);
            return;
        }
        
        $userId = $_SESSION['user_id'];
        $result = $this->model->getStatisticsByUserId($userId);
        $this->sendResponse($result);
    }
    
    /**
     * Lấy lịch sử thanh toán
     */
    private function handleGetMyPayments() {
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user_id'])) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Vui lòng đăng nhập'
            ]);
            return;
        }
        
        $userId = $_SESSION['user_id'];
        $result = $this->model->getPaymentsByUserId($userId);
        $this->sendResponse($result);
    }
    
    /**
     * Gửi JSON response
     */
    private function sendResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
}

// Khởi tạo và xử lý request
$controller = new CustomerRegistrationController();
$controller->handleRequest();
