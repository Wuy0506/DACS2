<?php
session_start();
require_once __DIR__ . '/../model/database.php';
require_once __DIR__ . '/../model/student/RepairRequestModel.php';

/**
 * Controller xử lý yêu cầu sửa chữa
 */
class RepairRequestController {
    private $repairModel;
    
    function __construct() {
        $this->repairModel = new RepairRequestModel();
    }
    
    /**
     * Điều hướng request
     */
    public function handleRequest() {
        $action = isset($_GET['action']) ? $_GET['action'] : '';
        
        switch ($action) {
            case 'get-student-rooms':
                $this->handleGetStudentRooms();
                break;
                
            case 'create-repair-request':
                $this->handleCreateRepairRequest();
                break;
                
            case 'get-student-requests':
                $this->handleGetStudentRequests();
                break;
                
            case 'upload-image':
                $this->handleUploadImage();
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
     * Lấy danh sách phòng của sinh viên
     */
    private function handleGetStudentRooms() {
        $studentId = isset($_GET['student_id']) ? intval($_GET['student_id']) : 0;
        
        if (empty($studentId)) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Thiếu thông tin sinh viên'
            ]);
            return;
        }
        
        $result = $this->repairModel->getStudentRooms($studentId);
        $this->sendResponse($result);
    }
    
    /**
     * Tạo yêu cầu sửa chữa mới
     */
    private function handleCreateRepairRequest() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            $data = $_POST;
        }
        
        $studentId = isset($data['student_id']) ? intval($data['student_id']) : 0;
        $roomId = isset($data['room_id']) ? intval($data['room_id']) : 0;
        $description = isset($data['description']) ? trim($data['description']) : '';
        $imageUrl = isset($data['image_url']) ? trim($data['image_url']) : '';
        $priority = isset($data['priority']) ? $data['priority'] : 'Thường';
        
        // Validate
        if (empty($studentId) || empty($roomId) || empty($description)) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Vui lòng điền đầy đủ thông tin'
            ]);
            return;
        }
        
        if (!in_array($priority, ['Thường', 'Khẩn cấp'])) {
            $priority = 'Thường';
        }
        
        $result = $this->repairModel->createRepairRequest(
            $studentId,
            $roomId,
            $description,
            $imageUrl,
            $priority
        );
        
        $this->sendResponse($result);
    }
    
    /**
     * Lấy danh sách yêu cầu của sinh viên
     */
    private function handleGetStudentRequests() {
        $studentId = isset($_GET['student_id']) ? intval($_GET['student_id']) : 0;
        $status = isset($_GET['status']) ? $_GET['status'] : null;
        
        if (empty($studentId)) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Thiếu thông tin sinh viên'
            ]);
            return;
        }
        
        $result = $this->repairModel->getStudentRepairRequests($studentId, $status);
        $this->sendResponse($result);
    }
    
    /**
     * Upload hình ảnh
     */
    private function handleUploadImage() {
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Không có file hoặc có lỗi khi upload'
            ]);
            return;
        }
        
        $file = $_FILES['image'];
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        // Validate file type
        if (!in_array($file['type'], $allowedTypes)) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Chỉ chấp nhận file ảnh (JPG, PNG, GIF)'
            ]);
            return;
        }
        
        // Validate file size
        if ($file['size'] > $maxSize) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Kích thước file không được vượt quá 5MB'
            ]);
            return;
        }
        
        // Create upload directory if not exists
        $uploadDir = __DIR__ . '/../../uploads/repairs/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'repair_' . time() . '_' . uniqid() . '.' . $extension;
        $filepath = $uploadDir . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            $imageUrl = '/uploads/repairs/' . $filename;
            $this->sendResponse([
                'success' => true,
                'message' => 'Upload ảnh thành công',
                'image_url' => $imageUrl
            ]);
        } else {
            $this->sendResponse([
                'success' => false,
                'message' => 'Lỗi khi lưu file'
            ]);
        }
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
$controller = new RepairRequestController();
$controller->handleRequest();
