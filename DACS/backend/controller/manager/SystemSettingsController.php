<?php
/**
 * System Settings Controller
 * Xử lý các request API cho chức năng cấu hình hệ thống của Manager
 */

session_start();
require_once __DIR__ . '/../../model/manager/SystemSettingsModel.php';

class SystemSettingsController {
    private $settingsModel;
    
    public function __construct() {
        $this->settingsModel = new SystemSettingsModel();
    }
    
    /**
     * Main request handler
     */
    public function handleRequest() {
        $action = isset($_GET['action']) ? $_GET['action'] : '';
        
        switch ($action) {
            case 'get-all-settings':
                $this->handleGetAllSettings();
                break;
                
            case 'get-setting':
                $this->handleGetSetting();
                break;
                
            case 'add-setting':
                $this->handleAddSetting();
                break;
                
            case 'update-setting':
                $this->handleUpdateSetting();
                break;
                
            case 'delete-setting':
                $this->handleDeleteSetting();
                break;
                
            default:
                $this->sendResponse([
                    'success' => false,
                    'message' => 'Invalid action'
                ]);
        }
    }
    
    /**
     * Lấy tất cả cấu hình
     */
    private function handleGetAllSettings() {
        $result = $this->settingsModel->getAllSettings();
        $this->sendResponse($result);
    }
    
    /**
     * Lấy chi tiết một cấu hình
     */
    private function handleGetSetting() {
        $settingId = isset($_GET['setting_id']) ? intval($_GET['setting_id']) : 0;
        $settingName = isset($_GET['setting_name']) ? $_GET['setting_name'] : '';
        
        if ($settingId > 0) {
            $result = $this->settingsModel->getSettingById($settingId);
        } else if (!empty($settingName)) {
            $result = $this->settingsModel->getSettingByName($settingName);
        } else {
            $this->sendResponse([
                'success' => false,
                'message' => 'Thiếu setting_id hoặc setting_name'
            ]);
            return;
        }
        
        $this->sendResponse($result);
    }
    
    /**
     * Thêm cấu hình mới
     */
    private function handleAddSetting() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            $data = $_POST;
        }
        
        // Validate dữ liệu
        if (empty($data['setting_name']) || empty($data['setting_value'])) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Thiếu setting_name hoặc setting_value'
            ]);
            return;
        }
        
        // Lấy manager_id từ session (giả sử đã đăng nhập)
        $managerId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;
        
        $result = $this->settingsModel->addSetting($data, $managerId);
        $this->sendResponse($result);
    }
    
    /**
     * Cập nhật cấu hình
     */
    private function handleUpdateSetting() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            $data = $_POST;
        }
        
        $settingId = isset($data['setting_id']) ? intval($data['setting_id']) : 0;
        $settingValue = isset($data['setting_value']) ? $data['setting_value'] : '';
        
        if (empty($settingId) || $settingValue === '') {
            $this->sendResponse([
                'success' => false,
                'message' => 'Thiếu setting_id hoặc setting_value'
            ]);
            return;
        }
        
        // Lấy manager_id từ session
        $managerId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;
        
        $result = $this->settingsModel->updateSetting($settingId, $settingValue, $managerId);
        $this->sendResponse($result);
    }
    
    /**
     * Xóa cấu hình
     */
    private function handleDeleteSetting() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            $data = $_POST;
        }
        
        $settingId = isset($data['setting_id']) ? intval($data['setting_id']) : 0;
        
        if (empty($settingId)) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Thiếu setting_id'
            ]);
            return;
        }
        
        $result = $this->settingsModel->deleteSetting($settingId);
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
$controller = new SystemSettingsController();
$controller->handleRequest();
?>
