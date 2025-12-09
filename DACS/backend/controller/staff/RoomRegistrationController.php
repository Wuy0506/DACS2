<?php
session_start();
require_once __DIR__ . '/../../model/database.php';
require_once __DIR__ . '/../../model/staff/RoomRegistrationModel.php';

/**
 * Controller xử lý quản lý đăng ký phòng (Staff/Manager)
 */
class RoomRegistrationController {
    private $model;
    
    function __construct() {
        $this->model = new RoomRegistrationModel();
    }
    
    /**
     * Điều hướng request
     */
    public function handleRequest() {
        $action = isset($_GET['action']) ? $_GET['action'] : '';
        
        // Kiểm tra Ajax request
        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
        
        switch ($action) {
            case 'get-all':
                $this->handleGetAll();
                break;
                
            case 'get-by-status':
                $this->handleGetByStatus();
                break;
                
            case 'get-detail':
                $this->handleGetDetail();
                break;
                
            case 'approve':
                $this->handleApprove();
                break;
                
            case 'reject':
                $this->handleReject();
                break;
                
            case 'get-statistics':
                $this->handleGetStatistics();
                break;
                
            case 'reset-to-pending':
                $this->handleResetToPending();
                break;
                
            case 'get-by-id':
                $this->handleGetById();
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
     * Lấy tất cả đăng ký
     */
    private function handleGetAll() {
        $result = $this->model->getAllRegistrations();
        $this->sendResponse($result);
    }
    
    /**
     * Lấy đăng ký theo trạng thái
     */
    private function handleGetByStatus() {
        $status = isset($_GET['status']) ? $_GET['status'] : null;
        
        if (empty($status)) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Thiếu tham số status'
            ]);
            return;
        }
        
        $result = $this->model->getAllRegistrations($status);
        $this->sendResponse($result);
    }
    
    /**
     * Lấy chi tiết đăng ký
     */
    private function handleGetDetail() {
        $registrationId = isset($_GET['registration_id']) ? intval($_GET['registration_id']) : 0;
        
        if (empty($registrationId)) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Thiếu registration_id'
            ]);
            return;
        }
        
        $result = $this->model->getRegistrationById($registrationId);
        $this->sendResponse($result);
    }
    
    /**
     * Duyệt đăng ký
     */
    private function handleApprove() {
        // Lấy dữ liệu từ POST
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            $data = $_POST;
        }
        
        $registrationId = isset($data['registration_id']) ? intval($data['registration_id']) : 0;
        $approvedBy = isset($data['approved_by']) ? intval($data['approved_by']) : null;
        
        // Kiểm tra session nếu không có approved_by
        if (empty($approvedBy) && isset($_SESSION['user_id'])) {
            $approvedBy = $_SESSION['user_id'];
        }
        
        if (empty($registrationId)) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Thiếu registration_id'
            ]);
            return;
        }
        
        if (empty($approvedBy)) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Không xác định được người duyệt'
            ]);
            return;
        }
        
        $result = $this->model->approveRegistration($registrationId, $approvedBy);
        $this->sendResponse($result);
    }
    
    /**
     * Từ chối đăng ký
     */
    private function handleReject() {
        // Lấy dữ liệu từ POST
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            $data = $_POST;
        }
        
        $registrationId = isset($data['registration_id']) ? intval($data['registration_id']) : 0;
        $rejectedBy = isset($data['rejected_by']) ? intval($data['rejected_by']) : null;
        
        // Kiểm tra session nếu không có rejected_by
        if (empty($rejectedBy) && isset($_SESSION['user_id'])) {
            $rejectedBy = $_SESSION['user_id'];
        }
        
        if (empty($registrationId)) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Thiếu registration_id'
            ]);
            return;
        }
        
        if (empty($rejectedBy)) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Không xác định được người từ chối'
            ]);
            return;
        }
        
        $result = $this->model->rejectRegistration($registrationId, $rejectedBy);
        $this->sendResponse($result);
    }
    
    /**
     * Lấy thống kê
     */
    private function handleGetStatistics() {
        $stats = $this->model->getStatistics();
        $this->sendResponse([
            'success' => true,
            'data' => $stats
        ]);
    }
    
    /**
     * Đặt lại trạng thái về chờ duyệt
     */
    private function handleResetToPending() {
        // Lấy dữ liệu từ POST
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            $data = $_POST;
        }
        
        $registrationId = isset($data['registration_id']) ? intval($data['registration_id']) : 0;
        
        if (empty($registrationId)) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Thiếu registration_id'
            ]);
            return;
        }
        
        $result = $this->model->resetToPending($registrationId);
        $this->sendResponse($result);
    }
    
    /**
     * Lấy đăng ký theo ID
     */
    private function handleGetById() {
        $registrationId = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if (empty($registrationId)) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Thiếu ID'
            ]);
            return;
        }
        
        $result = $this->model->getRegistrationById($registrationId);
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
$controller = new RoomRegistrationController();
$controller->handleRequest();
