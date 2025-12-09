<?php
/**
 * Manager Repair Controller
 * Xử lý các request API cho chức năng phê duyệt sửa chữa của Manager
 */

session_start();
require_once __DIR__ . '/../../model/manager/ManagerRepairModel.php';

class ManagerRepairController {
    private $repairModel;
    
    public function __construct() {
        $this->repairModel = new ManagerRepairModel();
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
                
            case 'approve-request':
                $this->handleApproveRequest();
                break;
                
            case 'reject-request':
                $this->handleRejectRequest();
                break;
                
            case 'request-changes':
                $this->handleRequestChanges();
                break;
                
            case 'get-statistics':
                $this->handleGetStatistics();
                break;
            
            case 'get-pending-approvals':
                $this->handleGetPendingApprovals();
                break;
                
            case 'approve':
                $this->handleApproveRequest();
                break;
                
            case 'reject':
                $this->handleRejectRequest();
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
     * Phê duyệt yêu cầu
     */
    private function handleApproveRequest() {
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
        
        // Lấy manager_id từ session
        $managerId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;
        
        // Chuẩn bị dữ liệu bổ sung (nếu có)
        $approveData = [];
        if (isset($data['estimated_cost'])) {
            $approveData['estimated_cost'] = $data['estimated_cost'];
        }
        if (isset($data['description'])) {
            $approveData['description'] = $data['description'];
        }
        
        $result = $this->repairModel->approveRepairRequest($repairId, $managerId, $approveData);
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
        $reason = isset($data['reason']) ? $data['reason'] : '';
        
        if (empty($repairId)) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Thiếu repair_id'
            ]);
            return;
        }
        
        // Lấy manager_id từ session
        $managerId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;
        
        $result = $this->repairModel->rejectRepairRequest($repairId, $managerId, $reason);
        $this->sendResponse($result);
    }
    
    /**
     * Yêu cầu điều chỉnh
     */
    private function handleRequestChanges() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            $data = $_POST;
        }
        
        $repairId = isset($data['repair_id']) ? intval($data['repair_id']) : 0;
        $changes = isset($data['changes']) ? $data['changes'] : '';
        
        if (empty($repairId) || empty($changes)) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Thiếu repair_id hoặc changes'
            ]);
            return;
        }
        
        // Lấy manager_id từ session
        $managerId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;
        
        $result = $this->repairModel->requestChanges($repairId, $managerId, $changes);
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
     * Lấy danh sách yêu cầu cần phê duyệt (Đang sửa - cần manager duyệt chi phí)
     */
    private function handleGetPendingApprovals() {
        $result = $this->repairModel->getPendingApprovals();
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
$controller = new ManagerRepairController();
$controller->handleRequest();
?>
