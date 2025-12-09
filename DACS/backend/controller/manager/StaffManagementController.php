<?php
/**
 * Staff Management Controller
 * Xử lý các request API cho chức năng quản lý nhân viên của Manager
 */

session_start();
require_once __DIR__ . '/../../model/manager/StaffManagementModel.php';

class StaffManagementController {
    private $staffModel;
    
    public function __construct() {
        $this->staffModel = new StaffManagementModel();
    }
    
    /**
     * Main request handler
     */
    public function handleRequest() {
        $action = isset($_GET['action']) ? $_GET['action'] : '';
        
        switch ($action) {
            case 'get-all-staff':
                $this->handleGetAllStaff();
                break;
                
            case 'get-staff-details':
                $this->handleGetStaffDetails();
                break;
                
            case 'add-staff':
                $this->handleAddStaff();
                break;
                
            case 'update-staff':
                $this->handleUpdateStaff();
                break;
                
            case 'delete-staff':
                $this->handleDeleteStaff();
                break;
                
            case 'get-statistics':
                $this->handleGetStatistics();
                break;
                
            default:
                $this->sendResponse([
                    'success' => false,
                    'message' => 'Invalid action'
                ]);
        }
    }
    
    /**
     * Lấy danh sách tất cả nhân viên
     */
    private function handleGetAllStaff() {
        $role = isset($_GET['role']) ? $_GET['role'] : 'all';
        
        $result = $this->staffModel->getAllStaff($role);
        $this->sendResponse($result);
    }
    
    /**
     * Lấy chi tiết nhân viên
     */
    private function handleGetStaffDetails() {
        $userId = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
        
        if (empty($userId)) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Thiếu user_id'
            ]);
            return;
        }
        
        $result = $this->staffModel->getStaffDetails($userId);
        $this->sendResponse($result);
    }
    
    /**
     * Thêm nhân viên mới
     */
    private function handleAddStaff() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            $data = $_POST;
        }
        
        // Validate dữ liệu
        $requiredFields = ['username', 'password', 'full_name', 'email', 'phone', 'role', 'hire_date'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $this->sendResponse([
                    'success' => false,
                    'message' => "Thiếu trường bắt buộc: $field"
                ]);
                return;
            }
        }
        
        // Validate role
        if (!in_array($data['role'], ['staff', 'manager'])) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Role không hợp lệ'
            ]);
            return;
        }
        
        // Thêm position cho staff hoặc department cho manager
        if ($data['role'] === 'staff' && empty($data['position'])) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Thiếu position cho staff'
            ]);
            return;
        }
        
        $result = $this->staffModel->addStaff($data);
        $this->sendResponse($result);
    }
    
    /**
     * Cập nhật thông tin nhân viên
     */
    private function handleUpdateStaff() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            $data = $_POST;
        }
        
        $userId = isset($data['user_id']) ? intval($data['user_id']) : 0;
        
        if (empty($userId)) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Thiếu user_id'
            ]);
            return;
        }
        
        // Validate dữ liệu
        $requiredFields = ['full_name', 'email', 'phone'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $this->sendResponse([
                    'success' => false,
                    'message' => "Thiếu trường bắt buộc: $field"
                ]);
                return;
            }
        }
        
        $result = $this->staffModel->updateStaff($userId, $data);
        $this->sendResponse($result);
    }
    
    /**
     * Xóa nhân viên
     */
    private function handleDeleteStaff() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            $data = $_POST;
        }
        
        $userId = isset($data['user_id']) ? intval($data['user_id']) : 0;
        
        if (empty($userId)) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Thiếu user_id'
            ]);
            return;
        }
        
        $result = $this->staffModel->deleteStaff($userId);
        $this->sendResponse($result);
    }
    
    /**
     * Lấy thống kê nhân viên
     */
    private function handleGetStatistics() {
        $result = $this->staffModel->getStaffStatistics();
        $this->sendResponse($result);
    }
    
    /**
     * Send JSON response
     */
    private function sendResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
}

// Execute controller
$controller = new StaffManagementController();
$controller->handleRequest();
?>
