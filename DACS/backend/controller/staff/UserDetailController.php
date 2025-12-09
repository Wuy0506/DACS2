<?php
session_start();
require_once __DIR__ . '/../model/database.php';
require_once __DIR__ . '/../model/staff/UserDetailModel.php';

/**
 * Controller xử lý chi tiết người dùng
 */
class UserDetailController {
    private $model;
    
    function __construct() {
        $this->model = new UserDetailModel();
    }
    
    /**
     * Điều hướng request
     */
    public function handleRequest() {
        $action = isset($_GET['action']) ? $_GET['action'] : '';
        
        switch ($action) {
            case 'get-user-detail':
                $this->handleGetUserDetail();
                break;
                
            case 'get-registration-history':
                $this->handleGetRegistrationHistory();
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
     * Lấy chi tiết người dùng
     */
    private function handleGetUserDetail() {
        $userId = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
        
        if (empty($userId)) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Thiếu user_id'
            ]);
            return;
        }
        
        $result = $this->model->getUserDetail($userId);
        $this->sendResponse($result);
    }
    
    /**
     * Lấy lịch sử đăng ký
     */
    private function handleGetRegistrationHistory() {
        $userId = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
        
        if (empty($userId)) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Thiếu user_id'
            ]);
            return;
        }
        
        $result = $this->model->getUserRegistrationHistory($userId);
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
