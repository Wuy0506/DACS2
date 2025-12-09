<?php
session_start();
require_once __DIR__ . '/../model/database.php';
require_once __DIR__ . '/../model/student/BookingModel.php';

/**
 * Controller xử lý đặt phòng
 */
class BookingController {
    private $bookingModel;
    
    function __construct() {
        $this->bookingModel = new BookingModel();
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
            case 'get-room-details':
                $this->handleGetRoomDetails($isAjax);
                break;
                
            case 'get-available-beds':
                $this->handleGetAvailableBeds($isAjax);
                break;
                
            case 'create-registration':
                $this->handleCreateRegistration($isAjax);
                break;
                
            case 'get-student-info':
                $this->handleGetStudentInfo($isAjax);
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
     * Lấy thông tin phòng và giường trống
     */
    private function handleGetRoomDetails($isAjax) {
        $roomId = isset($_GET['room_id']) ? intval($_GET['room_id']) : 0;
        
        if (empty($roomId)) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Thiếu thông tin phòng'
            ]);
            return;
        }
        
        $result = $this->bookingModel->getRoomDetails($roomId);
        $this->sendResponse($result);
    }
    
    /**
     * Lấy danh sách giường trống của phòng
     */
    private function handleGetAvailableBeds($isAjax) {
        $roomId = isset($_GET['room_id']) ? intval($_GET['room_id']) : 0;
        
        if (empty($roomId)) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Thiếu thông tin phòng'
            ]);
            return;
        }
        
        $beds = $this->bookingModel->getAvailableBeds($roomId);
        $this->sendResponse($beds);
    }
    
    /**
     * Tạo đăng ký phòng mới
     */
    private function handleCreateRegistration($isAjax) {
        
        // Lấy dữ liệu từ POST
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            // Fallback to $_POST if JSON decode fails
            $data = $_POST;
        }
        
        // Lấy user_id từ POST data (có thể null nếu người dùng chưa đăng nhập)
        $studentId = isset($data['user_id']) ? intval($data['user_id']) : null;
        
        // Cho phép đăng ký mà không cần user_id
        // Nếu không có user_id, đăng ký sẽ được tạo với trạng thái chờ duyệt
        
        $roomId = isset($data['room_id']) ? intval($data['room_id']) : 0;
        $bedId = isset($data['bed_id']) ? intval($data['bed_id']) : null;
        $startDate = isset($data['start_date']) ? $data['start_date'] : '';
        $endDate = isset($data['end_date']) ? $data['end_date'] : '';
        
        // Lấy thông tin sinh viên từ form
        $studentInfo = [
            'full_name' => isset($data['full_name']) ? trim($data['full_name']) : '',
            'email' => isset($data['email']) ? trim($data['email']) : '',
            'phone' => isset($data['phone']) ? trim($data['phone']) : '',
            'faculty' => isset($data['faculty']) ? trim($data['faculty']) : ''
        ];
        
        // Validate
        if (empty($roomId) || empty($bedId) || empty($startDate) || empty($endDate)) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Vui lòng điền đầy đủ thông tin và chọn giường'
            ]);
            return;
        }
        
        // Validate thông tin sinh viên (chỉ bắt buộc email và phone)
        if (empty($studentInfo['email']) || empty($studentInfo['phone'])) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Vui lòng điền Email và Số điện thoại'
            ]);
            return;
        }
        
        // Kiểm tra ngày hợp lệ
        $start = strtotime($startDate);
        $end = strtotime($endDate);
        
        if ($start >= $end) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Ngày kết thúc phải sau ngày bắt đầu'
            ]);
            return;
        }
        
        if ($start < strtotime('today')) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Ngày bắt đầu không được ở quá khứ'
            ]);
            return;
        }
        
        // Kiểm tra giới tính (chỉ khi có student_id)
        if (!empty($studentId)) {
            $genderCheck = $this->bookingModel->checkGenderRestriction($studentId, $roomId);
            if (!$genderCheck['success']) {
                $this->sendResponse($genderCheck);
                return;
            }
        }
        // Nếu không có student_id, bỏ qua kiểm tra giới tính (admin sẽ kiểm tra khi duyệt)
        
        // Tạo đăng ký với thông tin sinh viên và giường đã chọn
        $result = $this->bookingModel->createRoomRegistration(
            $studentId, 
            $roomId, 
            $bedId,
            $startDate, 
            $endDate,
            $studentInfo
        );
        
        $this->sendResponse($result);
    }
    
    /**
     * Lấy thông tin sinh viên để điền form
     * Action: get-student-info
     */
    private function handleGetStudentInfo($isAjax) {
        // Lấy user_id từ GET parameter
        $userId = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
        
        // Debug
        error_log("=== GET STUDENT INFO ===");
        error_log("User ID từ GET: " . $userId);
        
        if (empty($userId)) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Thiếu user_id'
            ]);
            return;
        }
        
        // Sử dụng hàm laythongtinnguoidung() từ Model
        $result = $this->bookingModel->laythongtinnguoidung($userId);
        
        // Debug
        error_log("Student info result: " . print_r($result, true));
        
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
$controller = new BookingController();
$controller->handleRequest();
