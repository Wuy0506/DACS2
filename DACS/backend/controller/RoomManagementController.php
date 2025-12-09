<?php
session_start();
require_once __DIR__ . '/../model/database.php';
require_once __DIR__ . '/../model/staff/RoomManagementModel.php';

/**
 * Controller xử lý quản lý phòng
 */
class RoomManagementController {
    private $model;
    
    function __construct() {
        $this->model = new RoomManagementModel();
    }
    
    /**
     * Điều hướng request
     */
    public function handleRequest() {
        $action = isset($_GET['action']) ? $_GET['action'] : '';
        
        switch ($action) {
            case 'get-all-rooms':
                $this->handleGetAllRooms();
                break;
                
            case 'get-room-detail':
                $this->handleGetRoomDetail();
                break;
                
            case 'update-room-status':
                $this->handleUpdateRoomStatus();
                break;
                
            case 'get-statistics':
                $this->handleGetStatistics();
                break;
                
            case 'search-students':
                $this->handleSearchStudents();
                break;
                
            case 'assign-student':
                $this->handleAssignStudent();
                break;
                
            case 'remove-student':
                $this->handleRemoveStudent();
                break;
                
            case 'get-available-beds':
                $this->handleGetAvailableBeds();
                break;
                
            case 'transfer-bed':
                $this->handleTransferBed();
                break;
                
            case 'create-room':
                $this->handleCreateRoom();
                break;
                
            case 'update-room':
                $this->handleUpdateRoom();
                break;
                
            case 'delete-room':
                $this->handleDeleteRoom();
                break;
                
            case 'get-room-by-id':
                $this->handleGetRoomById();
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
     * Lấy tất cả phòng
     */
    private function handleGetAllRooms() {
        $status = isset($_GET['status']) ? $_GET['status'] : null;
        $result = $this->model->getAllRooms($status);
        $this->sendResponse($result);
    }
    
    /**
     * Lấy chi tiết phòng
     */
    private function handleGetRoomDetail() {
        $roomId = isset($_GET['room_id']) ? intval($_GET['room_id']) : 0;
        
        if (empty($roomId)) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Thiếu room_id'
            ]);
            return;
        }
        
        $result = $this->model->getRoomDetail($roomId);
        $this->sendResponse($result);
    }
    
    /**
     * Cập nhật trạng thái phòng
     */
    private function handleUpdateRoomStatus() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            $data = $_POST;
        }
        
        $roomId = isset($data['room_id']) ? intval($data['room_id']) : 0;
        $status = isset($data['status']) ? $data['status'] : '';
        
        if (empty($roomId) || empty($status)) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Thiếu room_id hoặc status'
            ]);
            return;
        }
        
        $result = $this->model->updateRoomStatus($roomId, $status);
        $this->sendResponse($result);
    }
    
    /**
     * Lấy thống kê
     */
    private function handleGetStatistics() {
        $result = $this->model->getRoomStatistics();
        $this->sendResponse($result);
    }
    
    /**
     * Tìm kiếm sinh viên
     */
    private function handleSearchStudents() {
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        
        if (empty($keyword)) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Vui lòng nhập từ khóa tìm kiếm'
            ]);
            return;
        }
        
        $result = $this->model->searchStudents($keyword);
        $this->sendResponse($result);
    }
    
    /**
     * Thêm sinh viên vào phòng
     */
    private function handleAssignStudent() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            $data = $_POST;
        }
        
        $studentId = isset($data['student_id']) ? intval($data['student_id']) : 0;
        $roomId = isset($data['room_id']) ? intval($data['room_id']) : 0;
        $startDate = isset($data['start_date']) ? $data['start_date'] : date('Y-m-d');
        $endDate = isset($data['end_date']) ? $data['end_date'] : date('Y-m-d', strtotime('+1 year'));
        $staffId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1; // Default staff ID
        
        if (empty($studentId) || empty($roomId)) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Thiếu thông tin sinh viên hoặc phòng'
            ]);
            return;
        }
        
        $result = $this->model->assignStudentToRoom($studentId, $roomId, $startDate, $endDate, $staffId);
        $this->sendResponse($result);
    }
    
    /**
     * Xóa sinh viên khỏi phòng
     */
    private function handleRemoveStudent() {
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
        
        $result = $this->model->removeStudentFromRoom($registrationId);
        $this->sendResponse($result);
    }
    
    /**
     * Lấy danh sách giường trống trong phòng
     */
    private function handleGetAvailableBeds() {
        $roomId = isset($_GET['room_id']) ? intval($_GET['room_id']) : 0;
        
        if (empty($roomId)) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Thiếu room_id'
            ]);
            return;
        }
        
        $result = $this->model->getAvailableBeds($roomId);
        $this->sendResponse($result);
    }
    
    /**
     * Chuyển sinh viên sang giường khác
     */
    private function handleTransferBed() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            $data = $_POST;
        }
        
        $registrationId = isset($data['registration_id']) ? intval($data['registration_id']) : 0;
        $newBedId = isset($data['new_bed_id']) ? intval($data['new_bed_id']) : 0;
        
        if (empty($registrationId) || empty($newBedId)) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Thiếu thông tin registration_id hoặc new_bed_id'
            ]);
            return;
        }
        
        $result = $this->model->transferStudentBed($registrationId, $newBedId);
        $this->sendResponse($result);
    }
    
    /**
     * Thêm phòng mới
     */
    private function handleCreateRoom() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            $data = $_POST;
        }
        
        $result = $this->model->createRoom($data);
        $this->sendResponse($result);
    }
    
    /**
     * Cập nhật thông tin phòng
     */
    private function handleUpdateRoom() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            $data = $_POST;
        }
        
        $roomId = isset($data['room_id']) ? intval($data['room_id']) : 0;
        
        if (empty($roomId)) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Thiếu room_id'
            ]);
            return;
        }
        
        unset($data['room_id']); // Không cần room_id trong data update
        
        $result = $this->model->updateRoom($roomId, $data);
        $this->sendResponse($result);
    }
    
    /**
     * Xóa phòng
     */
    private function handleDeleteRoom() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            $data = $_POST;
        }
        
        $roomId = isset($data['room_id']) ? intval($data['room_id']) : 0;
        
        if (empty($roomId)) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Thiếu room_id'
            ]);
            return;
        }
        
        $result = $this->model->deleteRoom($roomId);
        $this->sendResponse($result);
    }
    
    /**
     * Lấy thông tin phòng theo ID
     */
    private function handleGetRoomById() {
        $roomId = isset($_GET['room_id']) ? intval($_GET['room_id']) : 0;
        
        if (empty($roomId)) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Thiếu room_id'
            ]);
            return;
        }
        
        $result = $this->model->getRoomById($roomId);
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
$controller = new RoomManagementController();
$controller->handleRequest();
