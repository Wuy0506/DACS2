<?php
include_once __DIR__ . '/../database.php';

// TODO: Uncomment khi cài đặt Composer và PhpSpreadsheet cho chức năng import Excel
// require_once __DIR__ . '/../../vendor/autoload.php';
// use PhpOffice\PhpSpreadsheet\IOFactory;

class UtilityInvoiceModel {
    private $conn;
    private $electricPrice = 3000;
    private $waterPrice = 20000;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
        $this->loadPricesFromSettings();
    }
    
    /**
     * Lấy giá điện/nước từ SystemSettings
     */
    private function loadPricesFromSettings() {
        $sql = "SELECT setting_name, setting_value FROM SystemSettings WHERE setting_name IN ('electric_price_per_kwh', 'water_price_per_m3')";
        $result = mysqli_query($this->conn, $sql);
        
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                if ($row['setting_name'] == 'electric_price_per_kwh') {
                    $this->electricPrice = (float)$row['setting_value'];
                } elseif ($row['setting_name'] == 'water_price_per_m3') {
                    $this->waterPrice = (float)$row['setting_value'];
                }
            }
        }
    }
    
    /**
     * Thêm thông tin giá và tổng tiền vào dữ liệu hóa đơn
     */
    private function enrichInvoiceData($invoice) {
        $invoice['electric_price_per_unit'] = $this->electricPrice;
        $invoice['water_price_per_unit'] = $this->waterPrice;
        $invoice['electric_amount'] = $invoice['electric_usage'] * $this->electricPrice;
        $invoice['water_amount'] = $invoice['water_usage'] * $this->waterPrice;
        $invoice['total_amount'] = $invoice['electric_amount'] + $invoice['water_amount'];
        return $invoice;
    }

    /**
     * Lấy danh sách tất cả hóa đơn
     */
    public function getAllInvoices($filters = []) {
        $sql = "
            SELECT 
                ui.*,
                CONCAT('Tòa ', r.building, ' - Tầng ', r.floor) as room_info,
                r.capacity
            FROM UtilityInvoice ui
            INNER JOIN Room r ON ui.room_id = r.room_id
            WHERE 1=1
        ";
        
        if (!empty($filters['status'])) {
            $status = mysqli_real_escape_string($this->conn, $filters['status']);
            $sql .= " AND ui.status = '$status'";
        }
        
        if (!empty($filters['month'])) {
            $month = (int)$filters['month'];
            $sql .= " AND ui.invoice_month = $month";
        }
        
        if (!empty($filters['year'])) {
            $year = (int)$filters['year'];
            $sql .= " AND ui.invoice_year = $year";
        }
        
        $sql .= " ORDER BY ui.created_date DESC";
        
        $result = mysqli_query($this->conn, $sql);
        
        if (!$result) {
            return ["status" => "error", "message" => mysqli_error($this->conn)];
        }

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $this->enrichInvoiceData($row);
        }

        return ["status" => "success", "data" => $data];
    }

    public function getUtilityPrices() {
        $sql = "SELECT setting_name, setting_value FROM SystemSettings WHERE setting_name IN ('electric_price_per_kwh', 'water_price_per_m3')";
        $result = mysqli_query($this->conn, $sql);

        if (!$result) {
            return ["status" => "error", "message" => mysqli_error($this->conn)];
        }

        $data = [
            'electric_price' => 3000,
            'water_price' => 20000
        ];

        while ($row = mysqli_fetch_assoc($result)) {
            if ($row['setting_name'] === 'electric_price_per_kwh') {
                $data['electric_price'] = (float)$row['setting_value'];
            }
            if ($row['setting_name'] === 'water_price_per_m3') {
                $data['water_price'] = (float)$row['setting_value'];
            }
        }

        return [
            "status" => "success",
            "data" => $data
        ];
    }

    public function getOccupiedRoomsByMonth($month, $year) {
        $month = (int)$month;
        $year = (int)$year;

        $sql = "
            SELECT
                r.room_id,
                r.building,
                r.floor,
                r.room_name,
                r.capacity,
                r.available_beds,
                COUNT(rr.registration_id) AS active_registrations
            FROM RoomRegistration rr
            INNER JOIN Room r ON rr.room_id = r.room_id
            INNER JOIN Contract c ON rr.registration_id = c.registration_id
            WHERE rr.status = 'Đã duyệt'
                AND c.status = 'Hiệu lực'
                AND (
                    (YEAR(rr.start_date) = $year AND MONTH(rr.start_date) <= $month)
                    OR (YEAR(rr.start_date) < $year)
                )
                AND (
                    rr.end_date IS NULL
                    OR rr.end_date = '0000-00-00'
                    OR (
                        YEAR(rr.end_date) > $year
                        OR (YEAR(rr.end_date) = $year AND MONTH(rr.end_date) >= $month)
                    )
                )
            GROUP BY r.room_id, r.building, r.floor, r.room_name, r.capacity, r.available_beds
            ORDER BY r.building, r.floor
        ";

        $result = mysqli_query($this->conn, $sql);

        if (!$result) {
            return ["status" => "error", "message" => mysqli_error($this->conn)];
        }

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }

        return ["status" => "success", "data" => $data];
    }

    /**
     * Lấy hóa đơn theo ID
     */
    public function getInvoiceById($invoice_id) {
        $invoice_id = (int)$invoice_id;
        
        $sql = "
            SELECT ui.*, CONCAT('Tòa ', r.building, ' - Tầng ', r.floor) as room_info
            FROM UtilityInvoice ui
            INNER JOIN Room r ON ui.room_id = r.room_id
            WHERE ui.invoice_id = $invoice_id
        ";
        
        $result = mysqli_query($this->conn, $sql);
        
        if (!$result) {
            return ["status" => "error", "message" => mysqli_error($this->conn)];
        }

        $data = mysqli_fetch_assoc($result);
        
        if (!$data) {
            return ["status" => "error", "message" => "Không tìm thấy hóa đơn"];
        }

        return ["status" => "success", "data" => $this->enrichInvoiceData($data)];
    }

    /**
     * Lấy hóa đơn chưa thanh toán của phòng
     */
    public function getPendingInvoicesByRoom($room_id) {
        $room_id = (int)$room_id;
        
        $sql = "
            SELECT ui.*
            FROM UtilityInvoice ui
            WHERE ui.room_id = $room_id 
            AND ui.status = 'Chờ thanh toán'
            ORDER BY ui.invoice_year DESC, ui.invoice_month DESC
        ";
        
        $result = mysqli_query($this->conn, $sql);
        
        if (!$result) {
            return ["status" => "error", "message" => mysqli_error($this->conn)];
        }

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $this->enrichInvoiceData($row);
        }

        return ["status" => "success", "data" => $data];
    }

    /**
     * Tạo hóa đơn mới
     */
    public function createInvoice($data) {
        $room_id = (int)$data['room_id'];
        $month = (int)$data['invoice_month'];
        $year = (int)$data['invoice_year'];
        $electric_usage = isset($data['electric_usage']) ? (float)$data['electric_usage'] : 0;
        $water_usage = isset($data['water_usage']) ? (float)$data['water_usage'] : 0;
        $notes = isset($data['notes']) ? mysqli_real_escape_string($this->conn, $data['notes']) : '';
        $created_by = isset($data['created_by']) ? (int)$data['created_by'] : null;
        
        // Kiểm tra hóa đơn đã tồn tại chưa
        $check_sql = "SELECT invoice_id FROM UtilityInvoice WHERE room_id = ? AND invoice_month = ? AND invoice_year = ?";
        $stmt = mysqli_prepare($this->conn, $check_sql);
        mysqli_stmt_bind_param($stmt, "iii", $room_id, $month, $year);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            return ["status" => "error", "message" => "Hóa đơn cho phòng này trong tháng $month/$year đã tồn tại"];
        }
        
        $sql = "INSERT INTO UtilityInvoice (room_id, invoice_month, invoice_year, electric_usage, water_usage, notes, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "iiiddsi", $room_id, $month, $year, $electric_usage, $water_usage, $notes, $created_by);
        
        if (mysqli_stmt_execute($stmt)) {
            return ["status" => "success", "message" => "Thêm hóa đơn thành công", "invoice_id" => mysqli_insert_id($this->conn)];
        }
        
        return ["status" => "error", "message" => mysqli_error($this->conn)];
    }
    
    /**
     * Lưu hàng loạt hóa đơn
     */
    public function saveBulkInvoices($invoices) {
        $successCount = 0;
        $failedCount = 0;
        $errors = [];
        
        mysqli_begin_transaction($this->conn);
        
        try {
            foreach ($invoices as $invoice) {
                $room_id = (int)$invoice['room_id'];
                $month = (int)$invoice['invoice_month'];
                $year = (int)$invoice['invoice_year'];
                $electric_usage = isset($invoice['electric_usage']) ? (float)$invoice['electric_usage'] : 0;
                $water_usage = isset($invoice['water_usage']) ? (float)$invoice['water_usage'] : 0;
                $created_by = isset($invoice['created_by']) ? (int)$invoice['created_by'] : null;
                $invoice_id = isset($invoice['invoice_id']) && $invoice['invoice_id'] ? (int)$invoice['invoice_id'] : null;
                
                if ($invoice_id) {
                    // Update existing invoice
                    $sql = "UPDATE UtilityInvoice SET electric_usage = ?, water_usage = ? WHERE invoice_id = ? AND status = 'Chờ thanh toán'";
                    $stmt = mysqli_prepare($this->conn, $sql);
                    mysqli_stmt_bind_param($stmt, "ddi", $electric_usage, $water_usage, $invoice_id);
                } else {
                    // Insert new invoice
                    $sql = "INSERT INTO UtilityInvoice (room_id, invoice_month, invoice_year, electric_usage, water_usage, created_by) 
                            VALUES (?, ?, ?, ?, ?, ?)
                            ON DUPLICATE KEY UPDATE electric_usage = VALUES(electric_usage), water_usage = VALUES(water_usage)";
                    $stmt = mysqli_prepare($this->conn, $sql);
                    mysqli_stmt_bind_param($stmt, "iiiddi", $room_id, $month, $year, $electric_usage, $water_usage, $created_by);
                }
                
                if (mysqli_stmt_execute($stmt)) {
                    $successCount++;
                } else {
                    $failedCount++;
                    $errors[] = "Phòng ID $room_id: " . mysqli_stmt_error($stmt);
                }
            }
            
            mysqli_commit($this->conn);
            
            return [
                "status" => "success",
                "message" => "Lưu thành công",
                "data" => [
                    "success" => $successCount,
                    "failed" => $failedCount,
                    "errors" => $errors
                ]
            ];
            
        } catch (Exception $e) {
            mysqli_rollback($this->conn);
            return [
                "status" => "error",
                "message" => "Lỗi: " . $e->getMessage()
            ];
        }
    }

    /**
     * Cập nhật hóa đơn
     */
    public function updateInvoice($data) {
        $invoice_id = (int)$data['invoice_id'];
        
        // Kiểm tra hóa đơn đã thanh toán chưa
        $check_sql = "SELECT status FROM UtilityInvoice WHERE invoice_id = ?";
        $stmt = mysqli_prepare($this->conn, $check_sql);
        mysqli_stmt_bind_param($stmt, "i", $invoice_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $invoice = mysqli_fetch_assoc($result);
        
        if (!$invoice) {
            return ["status" => "error", "message" => "Không tìm thấy hóa đơn"];
        }
        
        if ($invoice['status'] == 'Đã thanh toán') {
            return ["status" => "error", "message" => "Không thể sửa hóa đơn đã thanh toán"];
        }
        
        $electric_usage = isset($data['electric_usage']) ? (float)$data['electric_usage'] : 0;
        $water_usage = isset($data['water_usage']) ? (float)$data['water_usage'] : 0;
        $notes = isset($data['notes']) ? mysqli_real_escape_string($this->conn, $data['notes']) : '';
        
        $sql = "UPDATE UtilityInvoice SET electric_usage = ?, water_usage = ?, notes = ? WHERE invoice_id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ddsi", $electric_usage, $water_usage, $notes, $invoice_id);
        
        if (mysqli_stmt_execute($stmt)) {
            return ["status" => "success", "message" => "Cập nhật thành công"];
        }
        
        return ["status" => "error", "message" => mysqli_error($this->conn)];
    }

    /**
     * Import hóa đơn từ file Excel
     * Format: Tòa | Tầng | Tháng | Năm | Điện (kWh) | Nước (m³) | Ghi chú
     */
    public function importFromExcel($filePath, $userId) {
        // Kiểm tra xem PhpSpreadsheet đã được cài đặt chưa
        if (!class_exists('PhpOffice\PhpSpreadsheet\IOFactory')) {
            return [
                "status" => "error",
                "message" => "Chức năng import Excel chưa được cài đặt. Vui lòng cài đặt Composer và PhpSpreadsheet."
            ];
        }
        
        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            
            $dataRows = array_slice($rows, 1); // Bỏ header
            
            $totalRecords = count($dataRows);
            $successCount = 0;
            $failedCount = 0;
            $errors = [];
            
            mysqli_begin_transaction($this->conn);
            
            try {
                foreach ($dataRows as $index => $row) {
                    $rowNumber = $index + 2;
                    
                    if (empty($row[0]) || empty($row[1])) {
                        $errors[] = "Dòng $rowNumber: Thiếu thông tin tòa nhà hoặc tầng";
                        $failedCount++;
                        continue;
                    }
                    
                    $building = trim($row[0]);
                    $floor = (int)trim($row[1]);
                    $month = !empty($row[2]) ? (int)trim($row[2]) : date('n');
                    $year = !empty($row[3]) ? (int)trim($row[3]) : date('Y');
                    $electric_usage = !empty($row[4]) ? (float)trim($row[4]) : 0;
                    $water_usage = !empty($row[5]) ? (float)trim($row[5]) : 0;
                    $notes = !empty($row[6]) ? trim($row[6]) : '';
                    
                    // Tìm room_id
                    $room_sql = "SELECT room_id FROM Room WHERE building = ? AND floor = ?";
                    $stmt = mysqli_prepare($this->conn, $room_sql);
                    mysqli_stmt_bind_param($stmt, "si", $building, $floor);
                    mysqli_stmt_execute($stmt);
                    $room_result = mysqli_stmt_get_result($stmt);
                    $room = mysqli_fetch_assoc($room_result);
                    
                    if (!$room) {
                        $errors[] = "Dòng $rowNumber: Không tìm thấy phòng Tòa $building - Tầng $floor";
                        $failedCount++;
                        continue;
                    }
                    
                    $room_id = $room['room_id'];
                    
                    // Insert hoặc update
                    $insert_sql = "
                        INSERT INTO UtilityInvoice (
                            room_id, invoice_month, invoice_year,
                            electric_usage, water_usage,
                            created_by, notes
                        ) VALUES (?, ?, ?, ?, ?, ?, ?)
                        ON DUPLICATE KEY UPDATE
                            electric_usage = VALUES(electric_usage),
                            water_usage = VALUES(water_usage),
                            notes = VALUES(notes)
                    ";
                    
                    $stmt = mysqli_prepare($this->conn, $insert_sql);
                    mysqli_stmt_bind_param($stmt, "iiiddis", 
                        $room_id, $month, $year,
                        $electric_usage, $water_usage,
                        $userId, $notes
                    );
                    
                    if (mysqli_stmt_execute($stmt)) {
                        $successCount++;
                    } else {
                        $errors[] = "Dòng $rowNumber: " . mysqli_stmt_error($stmt);
                        $failedCount++;
                    }
                }
                
                mysqli_commit($this->conn);
                
                return [
                    "status" => "success",
                    "message" => "Import thành công",
                    "data" => [
                        "total" => $totalRecords,
                        "success" => $successCount,
                        "failed" => $failedCount,
                        "errors" => $errors
                    ]
                ];
                
            } catch (Exception $e) {
                mysqli_rollback($this->conn);
                throw $e;
            }
            
        } catch (Exception $e) {
            return [
                "status" => "error",
                "message" => "Lỗi khi đọc file Excel: " . $e->getMessage()
            ];
        }
    }

    /**
     * Thanh toán hóa đơn - Tạo 2 records trong Payment (Điện và Nước)
     * 1 người thanh toán = cả phòng đã thanh toán
     */
    public function payInvoice($invoice_id, $student_id) {
        mysqli_begin_transaction($this->conn);
        
        try {
            // Lấy thông tin hóa đơn
            $invoice_sql = "SELECT * FROM UtilityInvoice WHERE invoice_id = ?";
            $stmt = mysqli_prepare($this->conn, $invoice_sql);
            mysqli_stmt_bind_param($stmt, "i", $invoice_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $invoice = mysqli_fetch_assoc($result);
            
            if (!$invoice) {
                throw new Exception("Không tìm thấy hóa đơn");
            }
            
            if ($invoice['status'] == 'Đã thanh toán') {
                throw new Exception("Hóa đơn đã được thanh toán");
            }
            
            // Kiểm tra sinh viên có ở phòng này không
            $check_sql = "
                SELECT rr.registration_id, c.contract_id
                FROM RoomRegistration rr
                INNER JOIN Contract c ON rr.registration_id = c.registration_id
                WHERE rr.student_id = ? 
                AND rr.room_id = ? 
                AND rr.status = 'Đã duyệt'
                AND c.status = 'Hiệu lực'
                LIMIT 1
            ";
            $stmt = mysqli_prepare($this->conn, $check_sql);
            mysqli_stmt_bind_param($stmt, "ii", $student_id, $invoice['room_id']);
            mysqli_stmt_execute($stmt);
            $check_result = mysqli_stmt_get_result($stmt);
            $student_room = mysqli_fetch_assoc($check_result);
            
            if (!$student_room) {
                throw new Exception("Bạn không có quyền thanh toán hóa đơn của phòng này");
            }
            
            // Tính tiền
            $invoice = $this->enrichInvoiceData($invoice);
            
            // Tạo 2 payment records: Điện và Nước
            $payment_ids = [];
            
            // Payment cho điện
            if ($invoice['electric_amount'] > 0) {
                $electric_payment_sql = "
                    INSERT INTO Payment (
                        student_id, contract_id, payment_type, amount,
                        payment_date, payment_method, description
                    ) VALUES (?, ?, 'Điện', ?, NOW(), 'Chuyển khoản online', ?)
                ";
                $description = "Tiền điện tháng {$invoice['invoice_month']}/{$invoice['invoice_year']}";
                $stmt = mysqli_prepare($this->conn, $electric_payment_sql);
                mysqli_stmt_bind_param($stmt, "iids", 
                    $student_id, $student_room['contract_id'], 
                    $invoice['electric_amount'], $description
                );
                mysqli_stmt_execute($stmt);
                $payment_ids[] = mysqli_insert_id($this->conn);
            }
            
            // Payment cho nước
            if ($invoice['water_amount'] > 0) {
                $water_payment_sql = "
                    INSERT INTO Payment (
                        student_id, contract_id, payment_type, amount,
                        payment_date, payment_method, description
                    ) VALUES (?, ?, 'Nước', ?, NOW(), 'Chuyển khoản online', ?)
                ";
                $description = "Tiền nước tháng {$invoice['invoice_month']}/{$invoice['invoice_year']}";
                $stmt = mysqli_prepare($this->conn, $water_payment_sql);
                mysqli_stmt_bind_param($stmt, "iids", 
                    $student_id, $student_room['contract_id'], 
                    $invoice['water_amount'], $description
                );
                mysqli_stmt_execute($stmt);
                $payment_ids[] = mysqli_insert_id($this->conn);
            }
            
            // Cập nhật trạng thái hóa đơn (1 người trả = cả phòng đã trả)
            $update_sql = "UPDATE UtilityInvoice SET status = 'Đã thanh toán' WHERE invoice_id = ?";
            $stmt = mysqli_prepare($this->conn, $update_sql);
            mysqli_stmt_bind_param($stmt, "i", $invoice_id);
            mysqli_stmt_execute($stmt);
            
            mysqli_commit($this->conn);
            
            return [
                "status" => "success",
                "message" => "Thanh toán thành công",
                "data" => [
                    "invoice_id" => $invoice_id,
                    "payment_ids" => $payment_ids,
                    "total_amount" => $invoice['total_amount']
                ]
            ];
            
        } catch (Exception $e) {
            mysqli_rollback($this->conn);
            return [
                "status" => "error",
                "message" => $e->getMessage()
            ];
        }
    }

    /**
     * Xóa hóa đơn
     */
    public function deleteInvoice($invoice_id) {
        $invoice_id = (int)$invoice_id;
        
        $check_sql = "SELECT status FROM UtilityInvoice WHERE invoice_id = ?";
        $stmt = mysqli_prepare($this->conn, $check_sql);
        mysqli_stmt_bind_param($stmt, "i", $invoice_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $invoice = mysqli_fetch_assoc($result);
        
        if (!$invoice) {
            return ["status" => "error", "message" => "Không tìm thấy hóa đơn"];
        }
        
        if ($invoice['status'] == 'Đã thanh toán') {
            return ["status" => "error", "message" => "Không thể xóa hóa đơn đã thanh toán"];
        }
        
        $sql = "DELETE FROM UtilityInvoice WHERE invoice_id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $invoice_id);
        
        if (mysqli_stmt_execute($stmt)) {
            return ["status" => "success", "message" => "Xóa hóa đơn thành công"];
        }
        
        return ["status" => "error", "message" => mysqli_error($this->conn)];
    }
}
