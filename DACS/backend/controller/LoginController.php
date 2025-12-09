<?php
include_once __DIR__ . '/../model/login/UserModel.php';

/**
 * Controller xử lý đăng nhập và đăng ký
 */
class LoginController {
    private $userModel;
    
    function __construct() {
        $this->userModel = new UserModel();
        
        // Bắt đầu phiên làm việc nếu chưa có
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Hàm run chính - xử lý routing
     */
    function run() {
        $action = isset($_GET['action']) ? $_GET['action'] : 'home';
        
        // Kiểm tra nếu là AJAX request
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
        
        switch($action) {
            case 'home':
                // Hiển thị trang home.php
                include(__DIR__ . '/../../frontend/frontend-html/home.php');
                exit();
                break;
                
            case 'view':
                // Kiểm tra nếu đã đăng nhập thì chuyển đến dashboard
                if(isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true) {
                    header('location: ../frontend/staff_manager/dashboard.php');
                    exit();
                }
                // Hiển thị form đăng nhập
                include(__DIR__ . '/../../LoginDarkSunSet/login.php');
                exit();
                break;
                
            case 'login':
                if($isAjax || (isset($_POST['email']) && isset($_POST['password']))) {
                    // Lấy dữ liệu từ AJAX hoặc POST
                    $data = json_decode(file_get_contents('php://input'), true);
                    if (!$data) {
                        $data = $_POST; // Fallback cho form-data
                    }
                    
                    $username = isset($data['email']) ? $data['email'] : '';
                    $password = isset($data['password']) ? $data['password'] : '';
                    
                    if (empty($username) || empty($password)) {
                        $result = [
                            'success' => false,
                            'message' => 'Vui lòng nhập đầy đủ thông tin'
                        ];
                    } else {
                        $result = $this->userModel->login($username, $password);
                        
                        if($result['success']) {
                            // Lưu thông tin người dùng vào session
                            $_SESSION['user'] = $result['user'];
                            $_SESSION['user_id'] = $result['user']['user_id'];
                            $_SESSION['role'] = $result['user']['role'];
                            $_SESSION['is_logged_in'] = true;
                        }
                    }
                    
                    // Trả về JSON cho AJAX
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($result);
                    exit();
                } else {
                    include(__DIR__ . '/../../LoginDarkSunSet/login.php');
                    exit();
                }
                break;
                
            case 'register':
                if($isAjax || isset($_POST['username'])) {
                    // Lấy dữ liệu từ AJAX hoặc POST
                    $data = json_decode(file_get_contents('php://input'), true);
                    if (!$data) {
                        $data = $_POST; // Fallback cho form-data
                    }
                    
                    $username = isset($data['username']) ? $data['username'] : '';
                    $password = isset($data['password']) ? $data['password'] : '';
                    $fullName = isset($data['full_name']) ? $data['full_name'] : $username;
                    $email = isset($data['email']) ? $data['email'] : '';
                    $phone = isset($data['phone']) ? $data['phone'] : '';
                    
                    if (empty($username) || empty($password) || empty($email)) {
                        $result = [
                            'success' => false,
                            'message' => 'Vui lòng nhập đầy đủ thông tin'
                        ];
                    } else {
                        $role = 'student'; // Mặc định là sinh viên
                        $result = $this->userModel->register($username, $password, $fullName, $email, $phone, $role);
                    }
                    
                    // Trả về JSON cho AJAX
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($result);
                    exit();
                }
                break;
                
            case 'logout':
                // Bắt đầu session nếu chưa có
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }

                // Xóa dữ liệu session
                $_SESSION = [];

                // Xóa cookie session trên trình duyệt (nếu có)
                if (ini_get("session.use_cookies")) {
                    $params = session_get_cookie_params();
                    setcookie(session_name(), '', time() - 3600,
                        $params["path"], $params["domain"],
                        $params["secure"], $params["httponly"]
                    );
                }

                // Hủy phiên
                session_destroy();

                // Trả về kết quả
                if ($isAjax) {
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode([
                        'success' => true,
                        'message' => 'Đăng xuất thành công'
                    ]);
                } else {
                    header('location: auth.php?action=view');
                }
                exit();
                break;
                
            case 'check-status':
                // Kiểm tra trạng thái đăng nhập
                $result = [
                    'success' => true,
                    'is_logged_in' => $this->isLoggedIn(),
                    'user' => $this->getCurrentUser()
                ];
                
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode($result);
                exit();
                break;
                
            case 'dashboard':
                // Kiểm tra đăng nhập trước khi hiển thị dashboard
                if(!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
                    header('location: auth.php?action=view');
                    exit();
                }
                
                include(__DIR__ . '/../view/dashboard.php');
                exit();
                break;
                
            default:
                // Hiển thị trang home.php thay vì login
                include(__DIR__ . '/../../frontend/frontend-html/home.php');
                exit();
                break;
        }
    }
    
    /**
     * Kiểm tra xem người dùng đã đăng nhập chưa
     */
    function isLoggedIn() {
        return isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true;
    }
    
    /**
     * Lấy thông tin người dùng đang đăng nhập
     */
    function getCurrentUser() {
        return $_SESSION['user'] ?? null;
    }
}
?>
