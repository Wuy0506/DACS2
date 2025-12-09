<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/controller/ContractController.php';

$controller = new ContractController();

// Lấy phương thức HTTP
$method = $_SERVER['REQUEST_METHOD'];

// Xử lý các request
try {
    switch ($method) {
        case 'GET':
            if (isset($_GET['action'])) {
                $action = $_GET['action'];
                
                switch ($action) {
                    case 'getAll':
                        // Lấy tất cả hợp đồng
                        $result = $controller->getAllContracts();
                        echo json_encode($result);
                        break;
                        
                    case 'getById':
                        // Lấy hợp đồng theo ID
                        if (isset($_GET['id'])) {
                            $result = $controller->getContractById($_GET['id']);
                            echo json_encode($result);
                        } else {
                            echo json_encode(["status" => "error", "message" => "Thiếu ID hợp đồng"]);
                        }
                        break;
                        
                    case 'getAvailableRooms':
                        // Lấy danh sách phòng trống
                        $gender = isset($_GET['gender']) ? $_GET['gender'] : null;
                        $result = $controller->getAvailableRooms($gender);
                        echo json_encode($result);
                        break;
                        
                    case 'getStatistics':
                        // Lấy thống kê hợp đồng
                        $result = $controller->getStatistics();
                        echo json_encode($result);
                        break;
                        
                    default:
                        echo json_encode(["status" => "error", "message" => "Action không hợp lệ"]);
                }
            } else {
                // Mặc định lấy tất cả
                $result = $controller->getAllContracts();
                echo json_encode($result);
            }
            break;
            
        case 'POST':
            // Lấy dữ liệu JSON từ body
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['action'])) {
                echo json_encode(["status" => "error", "message" => "Thiếu action"]);
                break;
            }
            
            $action = $input['action'];
            
            switch ($action) {
                case 'update':
                    // Cập nhật/gia hạn hợp đồng
                    $result = $controller->updateContract($input);
                    echo json_encode($result);
                    break;
                    
                case 'extend':
                    // Gia hạn hợp đồng và tạo hóa đơn tiền phòng
                    if (isset($input['contract_id']) && isset($input['new_end_date'])) {
                        $extend_months = isset($input['extend_months']) ? (int)$input['extend_months'] : null;
                        $result = $controller->extendContract($input['contract_id'], $input['new_end_date'], $extend_months);
                        echo json_encode($result);
                    } else {
                        echo json_encode(["status" => "error", "message" => "Thiếu thông tin gia hạn"]);
                    }
                    break;
                    
                case 'changeRoom':
                    // Chuyển phòng
                    $result = $controller->changeRoom($input);
                    echo json_encode($result);
                    break;
                    
                case 'terminate':
                    // Chấm dứt hợp đồng
                    if (isset($input['contract_id'])) {
                        $reason = isset($input['reason']) ? $input['reason'] : '';
                        $result = $controller->terminateContract($input['contract_id'], $reason);
                        echo json_encode($result);
                    } else {
                        echo json_encode(["status" => "error", "message" => "Thiếu ID hợp đồng"]);
                    }
                    break;
                    
                default:
                    echo json_encode(["status" => "error", "message" => "Action không hợp lệ"]);
            }
            break;
            
        case 'DELETE':
            // Xóa hợp đồng
            if (isset($_GET['id'])) {
                $result = $controller->deleteContract($_GET['id']);
                echo json_encode($result);
            } else {
                echo json_encode(["status" => "error", "message" => "Thiếu ID hợp đồng"]);
            }
            break;
            
        default:
            echo json_encode(["status" => "error", "message" => "Phương thức không được hỗ trợ"]);
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
