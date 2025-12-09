<?php
include_once __DIR__ . '/../model/student/SearchRoom.php';

/**
 * Controller xử lý tìm kiếm phòng
 */
class SearchRoomController {
    private $searchRoomModel;
    
    function __construct() {
        $this->searchRoomModel = new SearchRoomModel();
        
        // Bắt đầu phiên làm việc nếu chưa có
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Hàm run chính - xử lý routing
     */
    function run() {
        $action = isset($_GET['action']) ? $_GET['action'] : 'search';
        
        // Kiểm tra nếu là AJAX request
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
        
        switch($action) {
            case 'search':
                $this->handleSearch($isAjax);
                break;
                
            case 'get-room':
                $this->handleGetRoom($isAjax);
                break;
                
            case 'get-buildings':
                $this->handleGetBuildings($isAjax);
                break;
                
            case 'get-rooms-by-building':
                $this->handleGetRoomsByBuilding($isAjax);
                break;
                
            case 'get-rooms-by-gender':
                $this->handleGetRoomsByGender($isAjax);
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
     * Xử lý tìm kiếm phòng
     */
    private function handleSearch($isAjax) {
        // Lấy tham số từ GET hoặc POST
        $people = isset($_GET['people']) ? intval($_GET['people']) : 1;
        $gender = isset($_GET['gender']) ? $_GET['gender'] : '';
        $status = isset($_GET['status']) ? $_GET['status'] : '';
        
        // Nếu là POST request (JSON)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            if ($data) {
                $people = isset($data['people']) ? intval($data['people']) : $people;
                $gender = isset($data['gender']) ? $data['gender'] : $gender;
                $status = isset($data['status']) ? $data['status'] : $status;
            }
        }
        
        // Gọi model để tìm kiếm với các filter
        $result = $this->searchRoomModel->searchRooms($people, $gender, $status);
        
        $this->sendResponse($result);
    }
    
    /**
     * Xử lý lấy thông tin chi tiết phòng
     */
    private function handleGetRoom($isAjax) {
        if ($isAjax || isset($_GET['id'])) {
            $roomId = isset($_GET['id']) ? $_GET['id'] : '';
            
            if (empty($roomId)) {
                $this->sendResponse([
                    'success' => false,
                    'message' => 'ID phòng không hợp lệ'
                ]);
                return;
            }
            
            $result = $this->searchRoomModel->getRoomById($roomId);
            $this->sendResponse($result);
        } else {
            $this->sendResponse([
                'success' => false,
                'message' => 'Yêu cầu không hợp lệ'
            ]);
        }
    }
    
    /**
     * Xử lý lấy danh sách khu nhà (buildings)
     */
    private function handleGetBuildings($isAjax) {
        $result = $this->searchRoomModel->getAllBuildings();
        $this->sendResponse($result);
    }
    
    /**
     * Xử lý lấy danh sách phòng theo khu nhà
     */
    private function handleGetRoomsByBuilding($isAjax) {
        if ($isAjax || isset($_GET['building'])) {
            $building = isset($_GET['building']) ? $_GET['building'] : '';
            
            if (empty($building)) {
                $this->sendResponse([
                    'success' => false,
                    'message' => 'Khu nhà không hợp lệ'
                ]);
                return;
            }
            
            $result = $this->searchRoomModel->getRoomsByBuilding($building);
            $this->sendResponse($result);
        } else {
            $this->sendResponse([
                'success' => false,
                'message' => 'Yêu cầu không hợp lệ'
            ]);
        }
    }
    
    /**
     * Xử lý lấy danh sách phòng theo giới tính
     */
    private function handleGetRoomsByGender($isAjax) {
        if ($isAjax || isset($_GET['gender'])) {
            $gender = isset($_GET['gender']) ? $_GET['gender'] : '';
            
            if (empty($gender)) {
                $this->sendResponse([
                    'success' => false,
                    'message' => 'Giới tính không hợp lệ'
                ]);
                return;
            }
            
            $result = $this->searchRoomModel->getRoomsByGender($gender);
            $this->sendResponse($result);
        } else {
            $this->sendResponse([
                'success' => false,
                'message' => 'Yêu cầu không hợp lệ'
            ]);
        }
    }
    
    /**
     * Gửi response dạng JSON
     * @param array $data Dữ liệu cần trả về
     */
    private function sendResponse($data) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit();
    }
    
    /**
     * Kiểm tra người dùng đã đăng nhập chưa
     * @return bool
     */
    private function isLoggedIn() {
        return isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true;
    }
    
    /**
     * Lấy thông tin người dùng hiện tại
     * @return array|null
     */
    private function getCurrentUser() {
        if ($this->isLoggedIn()) {
            return isset($_SESSION['user']) ? $_SESSION['user'] : null;
        }
        return null;
    }
}
