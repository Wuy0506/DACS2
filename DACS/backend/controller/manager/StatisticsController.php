<?php
/**
 * Statistics Controller
 * Xử lý các request API cho chức năng thống kê của Manager
 */

session_start();
require_once __DIR__ . '/../../model/manager/StatisticsModel.php';

class StatisticsController {
    private $statsModel;
    
    public function __construct() {
        $this->statsModel = new StatisticsModel();
    }
    
    /**
     * Main request handler
     */
    public function handleRequest() {
        $action = isset($_GET['action']) ? $_GET['action'] : '';
        
        switch ($action) {
            case 'get-overall-statistics':
                $this->handleGetOverallStatistics();
                break;
                
            case 'get-revenue-statistics':
                $this->handleGetRevenueStatistics();
                break;
                
            case 'get-repair-cost-statistics':
                $this->handleGetRepairCostStatistics();
                break;
                
            case 'get-monthly-statistics':
                $this->handleGetMonthlyStatistics();
                break;
                
            case 'get-room-statistics-by-building':
                $this->handleGetRoomStatisticsByBuilding();
                break;
                
            default:
                $this->sendResponse([
                    'success' => false,
                    'message' => 'Invalid action'
                ]);
        }
    }
    
    /**
     * Lấy tổng quan thống kê
     */
    private function handleGetOverallStatistics() {
        $result = $this->statsModel->getOverallStatistics();
        $this->sendResponse($result);
    }
    
    /**
     * Lấy thống kê doanh thu
     */
    private function handleGetRevenueStatistics() {
        $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : null;
        $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : null;
        
        $result = $this->statsModel->getRevenueStatistics($startDate, $endDate);
        $this->sendResponse($result);
    }
    
    /**
     * Lấy thống kê chi phí sửa chữa
     */
    private function handleGetRepairCostStatistics() {
        $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : null;
        $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : null;
        
        $result = $this->statsModel->getRepairCostStatistics($startDate, $endDate);
        $this->sendResponse($result);
    }
    
    /**
     * Lấy thống kê theo tháng
     */
    private function handleGetMonthlyStatistics() {
        $year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
        
        $result = $this->statsModel->getMonthlyStatistics($year);
        $this->sendResponse($result);
    }
    
    /**
     * Lấy thống kê phòng theo tòa nhà
     */
    private function handleGetRoomStatisticsByBuilding() {
        $result = $this->statsModel->getRoomStatisticsByBuilding();
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
$controller = new StatisticsController();
$controller->handleRequest();
?>
