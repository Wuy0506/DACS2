<?php
/**
 * Staff Repair Request Controller
 * Xử lý các request API cho chức năng quản lý sửa chữa của Staff
 */

session_start();
require_once __DIR__ . '/../../model/staff/StaffRepairModel.php';

class StaffRepairController {
    private $repairModel;
    
    public function __construct() {
        $this->repairModel = new StaffRepairModel();
    }
    
    /**
     * Main request handler
     */
    public function handleRequest() {
        $action = isset($_GET['action']) ? $_GET['action'] : '';
        
        switch ($action) {
            case 'get-all-requests':
                $this->handleGetAllRequests();
                break;
                
            case 'get-request-details':
                $this->handleGetRequestDetails();
                break;
                
            case 'accept-request':
                $this->handleAcceptRequest();
                break;
                
            case 'complete-request':
                $this->handleCompleteRequest();
                break;
                
            case 'reject-request':
                $this->handleRejectRequest();
                break;
                
            case 'get-statistics':
                $this->handleGetStatistics();
                break;
                
            case 'get-staff-list':
                $this->handleGetStaffList();
                break;
                
            default:
                $this->sendResponse([
                    'success' => false,
                    'message' => 'Invalid action'
                ]);
        }
    }
    
    /**
     * Lấy tất cả yêu cầu sửa chữa
     */
    private function handleGetAllRequests() {
        $status = isset($_GET['status']) ? $_GET['status'] : null;
        
        $result = $this->repairModel->getAllRepairRequests($status);
        $this->sendResponse($result);
    }
    
    /**
     * Lấy chi tiết yêu cầu
     */
    private function handleGetRequestDetails() {
        $repairId = isset($_GET['repair_id']) ? intval($_GET['repair_id']) : 0;
        
        if (empty($repairId)) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Thiếu repair_id'
            ]);
            return;
        }
        
        $result = $this->repairModel->getRepairRequestDetails($repairId);
        $this->sendResponse($result);
    }
    
    /**
     * Staff tiếp nhận yêu cầu (Mở rộng)
     */
    private function handleAcceptRequest() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            $data = $_POST;
        }
        
        $repairId = isset($data['repair_id']) ? intval($data['repair_id']) : 0;
        $staffId = isset($data['staff_id']) ? intval($data['staff_id']) : 0;
        
        // Nếu không có staff_id, lấy từ session
        if (empty($staffId)) {
            $staffId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
        }
        
        if (empty($repairId)) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Thiếu thông tin repair_id'
            ]);
            return;
        }
        
        // Chuẩn bị dữ liệu tiếp nhận
        $acceptData = [];
        
        if (isset($data['priority']) && !empty($data['priority'])) {
            $acceptData['priority'] = $data['priority'];
        }
        
        if (isset($data['staff_notes']) && !empty($data['staff_notes'])) {
            $acceptData['staff_notes'] = trim($data['staff_notes']);
        }
        
        if (isset($data['assigned_to']) && !empty($data['assigned_to'])) {
            $acceptData['assigned_to'] = trim($data['assigned_to']);
        }
        
        if (isset($data['estimated_cost']) && !empty($data['estimated_cost'])) {
            $acceptData['estimated_cost'] = floatval($data['estimated_cost']);
        }
        
        if (isset($data['repair_report']) && !empty($data['repair_report'])) {
            $acceptData['repair_report'] = trim($data['repair_report']);
        }
        
        $result = $this->repairModel->acceptRepairRequest($repairId, $staffId, $acceptData);
        $this->sendResponse($result);
    }
    
    /**
     * Hoàn thành yêu cầu
     */
    private function handleCompleteRequest() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            $data = $_POST;
        }
        
        $repairId = isset($data['repair_id']) ? intval($data['repair_id']) : 0;
        $actualCost = isset($data['actual_cost']) ? floatval($data['actual_cost']) : null;
        
        if (empty($repairId)) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Thiếu repair_id'
            ]);
            return;
        }
        
        $result = $this->repairModel->completeRepairRequest($repairId, $actualCost);
        $this->sendResponse($result);
    }
    
    /**
     * Từ chối yêu cầu
     */
    private function handleRejectRequest() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            $data = $_POST;
        }
        
        $repairId = isset($data['repair_id']) ? intval($data['repair_id']) : 0;
        
        if (empty($repairId)) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Thiếu repair_id'
            ]);
            return;
        }
        
        $result = $this->repairModel->rejectRepairRequest($repairId);
        $this->sendResponse($result);
    }
    
    /**
     * Lấy thống kê
     */
    private function handleGetStatistics() {
        $result = $this->repairModel->getRepairStatistics();
        $this->sendResponse($result);
    }

    /**
     * Lấy danh sách nhân viên để chỉ định
     */
    private function handleGetStaffList() {
        $result = $this->repairModel->getStaffList();
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
$controller = new StaffRepairController();
$controller->handleRequest();
?>
