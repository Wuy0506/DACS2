<?php
session_start();
require_once __DIR__ . '/../../model/student/roomOfStudentModel.php';


class RoomOfStudentController {
    private $model;

    public function __construct() {
        $this->model = new RoomOfStudentModel();
    }

    public function handleRequest() {
        $action = isset($_GET['action']) ? $_GET['action'] : '';
        
        switch ($action) {
            case 'layThongTin':
                $this->getMeAndRoomAndRoommates();
                break;
            default:
                $this->sendResponse([
                    'success' => false,
                    'message' => 'Action không hợp lệ'
                ]);
                break;
        }
    }

    private function getMeAndRoomAndRoommates() {
        if (!isset($_SESSION['user_id'])) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Chưa đăng nhập'
            ]);
            return;
        }

        $userId = $_SESSION['user_id'];
        $data = $this->model->getRoomAndRoommates($userId);

        $this->sendResponse([
            'success' => true,
            'data' => $data
        ]);
    }

    private function sendResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
}

?>