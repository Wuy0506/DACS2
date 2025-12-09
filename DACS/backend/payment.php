<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/controller/PaymentController.php';

$controller = new PaymentController();

// Lấy phương thức HTTP
$method = $_SERVER['REQUEST_METHOD'];

// Xử lý các request thông qua controller
try {
    switch ($method) {
        case 'GET':
            $result = $controller->handleGet($_GET);
            echo json_encode($result);
            break;

        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true) ?? [];
            $result = $controller->handlePost($input);
            echo json_encode($result);
            break;

        default:
            echo json_encode(["status" => "error", "message" => "Phương thức không được hỗ trợ"]);
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
