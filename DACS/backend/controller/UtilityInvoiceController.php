<?php
require_once __DIR__ . '/../model/staff/UtilityInvoiceModel_v2.php';

class UtilityInvoiceController {
    private $model;

    public function __construct() {
        $this->model = new UtilityInvoiceModel();
    }

    public function handleRequest() {
        $action = isset($_GET['action']) ? $_GET['action'] : '';
        
        switch ($action) {
            case 'get-all-invoices':
                $this->handleGetAllInvoices();
                break;
            case 'get-invoice-by-id':
            case 'get-invoice':
                $this->handleGetInvoiceById();
                break;
            case 'get-pending-by-room':
                $this->handleGetPendingByRoom();
                break;
            case 'get-occupied-rooms':
                $this->handleGetOccupiedRooms();
                break;
            case 'get-utility-prices':
                $this->handleGetUtilityPrices();
                break;
            case 'create':
                $this->handleCreate();
                break;
            case 'update':
                $this->handleUpdate();
                break;
            case 'save-bulk':
                $this->handleSaveBulk();
                break;
            case 'import-excel':
                $this->handleImportExcel();
                break;
            case 'pay-invoice':
                $this->handlePayInvoice();
                break;
            case 'delete-invoice':
                $this->handleDeleteInvoice();
                break;
            default:
                echo json_encode(["status" => "error", "message" => "Action không hợp lệ"]);
        }
    }

    private function handleGetAllInvoices() {
        $filters = [];
        
        if (isset($_GET['status'])) {
            $filters['status'] = $_GET['status'];
        }
        if (isset($_GET['month'])) {
            $filters['month'] = $_GET['month'];
        }
        if (isset($_GET['year'])) {
            $filters['year'] = $_GET['year'];
        }
        if (isset($_GET['room_id'])) {
            $filters['room_id'] = $_GET['room_id'];
        }
        
        $result = $this->model->getAllInvoices($filters);
        echo json_encode($result);
    }

    private function handleGetInvoiceById() {
        if (!isset($_GET['invoice_id'])) {
            echo json_encode(["status" => "error", "message" => "Thiếu invoice_id"]);
            return;
        }
        
        $result = $this->model->getInvoiceById($_GET['invoice_id']);
        echo json_encode($result);
    }

    private function handleGetPendingByRoom() {
        if (!isset($_GET['room_id'])) {
            echo json_encode(["status" => "error", "message" => "Thiếu room_id"]);
            return;
        }
        
        $result = $this->model->getPendingInvoicesByRoom($_GET['room_id']);
        echo json_encode($result);
    }

    private function handleGetOccupiedRooms() {
        $month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('n');
        $year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

        $result = $this->model->getOccupiedRoomsByMonth($month, $year);
        echo json_encode($result);
    }

    private function handleGetUtilityPrices() {
        $result = $this->model->getUtilityPrices();
        echo json_encode($result);
    }

    private function handleCreate() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['room_id']) || !isset($data['invoice_month']) || !isset($data['invoice_year'])) {
            echo json_encode(["status" => "error", "message" => "Thiếu thông tin bắt buộc"]);
            return;
        }
        
        $result = $this->model->createInvoice($data);
        echo json_encode($result);
    }

    private function handleUpdate() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['invoice_id'])) {
            echo json_encode(["status" => "error", "message" => "Thiếu invoice_id"]);
            return;
        }
        
        $result = $this->model->updateInvoice($data);
        echo json_encode($result);
    }

    private function handleSaveBulk() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['invoices']) || !is_array($data['invoices'])) {
            echo json_encode(["status" => "error", "message" => "Thiếu dữ liệu hóa đơn"]);
            return;
        }
        
        $result = $this->model->saveBulkInvoices($data['invoices']);
        echo json_encode($result);
    }

    private function handleImportExcel() {
        if (!isset($_FILES['excel_file'])) {
            echo json_encode(["status" => "error", "message" => "Không có file được upload"]);
            return;
        }
        
        if (!isset($_POST['user_id'])) {
            echo json_encode(["status" => "error", "message" => "Thiếu thông tin user_id"]);
            return;
        }
        
        $file = $_FILES['excel_file'];
        
        // Validate file type
        $allowedExtensions = ['xls', 'xlsx'];
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($fileExtension, $allowedExtensions)) {
            echo json_encode(["status" => "error", "message" => "File phải là định dạng Excel (.xls hoặc .xlsx)"]);
            return;
        }
        
        // Validate file size (max 5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            echo json_encode(["status" => "error", "message" => "File không được vượt quá 5MB"]);
            return;
        }
        
        // Move file to temp location
        $uploadDir = __DIR__ . '/../../uploads/temp/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $tempFilePath = $uploadDir . uniqid() . '_' . $file['name'];
        
        if (!move_uploaded_file($file['tmp_name'], $tempFilePath)) {
            echo json_encode(["status" => "error", "message" => "Không thể upload file"]);
            return;
        }
        
        // Process the file
        $result = $this->model->importFromExcel($tempFilePath, $_POST['user_id']);
        
        // Delete temp file
        unlink($tempFilePath);
        
        echo json_encode($result);
    }

    private function handlePayInvoice() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['invoice_id']) || !isset($data['student_id'])) {
            echo json_encode(["status" => "error", "message" => "Thiếu thông tin invoice_id hoặc student_id"]);
            return;
        }
        
        $result = $this->model->payInvoice($data['invoice_id'], $data['student_id']);
        echo json_encode($result);
    }

    private function handleDeleteInvoice() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['invoice_id'])) {
            echo json_encode(["status" => "error", "message" => "Thiếu invoice_id"]);
            return;
        }
        
        $result = $this->model->deleteInvoice($data['invoice_id']);
        echo json_encode($result);
    }

    // private function handleUpdateOverdue() {
    //     $result = $this->model->updateOverdueStatus();
    //     echo json_encode($result);
    // }
}

// Khởi tạo controller và xử lý request
$controller = new UtilityInvoiceController();
$controller->handleRequest();
