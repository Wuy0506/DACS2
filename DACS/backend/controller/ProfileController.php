<?php
require_once __DIR__ . '/../model/student/ProfileModel.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


class ProfileController {
    private $profileModel;
    
    public function __construct() {
        $this->profileModel = new ProfileModel();
    }
    
    public function run() {
        $action = isset($_GET['action']) ? $_GET['action'] : '';
        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
        
        switch ($action) {
            case 'get-profile':
                $this->handleGetProfile($isAjax);
                break;
            case 'update-profile':
                $this->handleUpdateProfile($isAjax);
                break;
            case 'change-password':
                $this->handleChangePassword($isAjax);
                break;
            case 'check-profile-complete':
                $this->handleCheckProfileComplete($isAjax);
                break;
            default:
                $this->sendResponse([
                    'success' => false,
                    'message' => 'Invalid action'
                ]);
        }
    }
    
    private function handleGetProfile($isAjax) {
        // Lấy user_id từ GET hoặc session
        $userId = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
        
        if (empty($userId)) {
            // Thử lấy từ session
            session_start();
            $userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
        }
        
        if (empty($userId)) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để xem thông tin'
            ]);
            return;
        }
        
        $result = $this->profileModel->getUserProfile($userId);
        $this->sendResponse($result);
    }
    
    private function handleUpdateProfile($isAjax) {
        // Lấy dữ liệu từ POST
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            $data = $_POST;
        }
        
        $userId = isset($data['user_id']) ? intval($data['user_id']) : 0;
        
        if (empty($userId)) {
            // Thử lấy từ session
            session_start();
            $userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
        }
        
        if (empty($userId)) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để cập nhật thông tin'
            ]);
            return;
        }
        
        // Chuẩn bị dữ liệu profile
        $profileData = [
            'full_name' => isset($data['full_name']) ? $data['full_name'] : '',
            'email' => isset($data['email']) ? $data['email'] : '',
            'phone' => isset($data['phone']) ? $data['phone'] : '',
'student_id' => isset($data['student_id']) ? $data['student_id'] : '',
            'faculty' => isset($data['faculty']) ? $data['faculty'] : '',
            'major' => isset($data['major']) ? $data['major'] : '',
            'gender' => isset($data['gender']) ? $data['gender'] : '',
            'date_of_birth' => isset($data['date_of_birth']) ? $data['date_of_birth'] : '',
            'address' => isset($data['address']) ? $data['address'] : ''
        ];
        
        $result = $this->profileModel->updateProfile($userId, $profileData);
        if ($result['success']) {
            // Reload user mới nhất từ DB vào session
            $freshUser = $this->profileModel->getUserProfile($userId);
            if ($freshUser['success']) {
                $_SESSION['user'] = $freshUser['data'];
            }
        }
        $this->sendResponse($result);
    }
    
    private function handleChangePassword($isAjax) {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            $data = $_POST;
        }
        
        $userId = isset($data['user_id']) ? intval($data['user_id']) : 0;
        
        if (empty($userId)) {
            session_start();
            $userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
        }
        
        if (empty($userId)) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Vui lòng đăng nhập'
            ]);
            return;
        }
        
        $oldPassword = isset($data['old_password']) ? $data['old_password'] : '';
        $newPassword = isset($data['new_password']) ? $data['new_password'] : '';
        
        if (empty($oldPassword) || empty($newPassword)) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Vui lòng điền đầy đủ thông tin'
            ]);
            return;
        }
        
        $result = $this->profileModel->changePassword($userId, $oldPassword, $newPassword);
        $this->sendResponse($result);
    }
    
    private function handleCheckProfileComplete($isAjax) {
        // Lấy user_id từ GET hoặc session
        $userId = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
        
        if (empty($userId)) {
            // Thử lấy từ session
            session_start();
            $userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
        }
        
        if (empty($userId)) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để kiểm tra thông tin'
            ]);
            return;
        }
        
        $result = $this->profileModel->checkProfileComplete($userId);
        $this->sendResponse($result);
    }
    
    private function sendResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
}
}
?>