
<!-- 
// cron_daily.php

// 1. Kết nối CSDL & Model
// Đảm bảo đường dẫn này trỏ đúng đến file database.php và ContractModel.php của bạn
require_once '/backend/database.php'; 
require_once '/backend/model/staff/ContractModel.php';

// Nếu bạn dùng PHPMailer, require nó ở đây
// require 'vendor/autoload.php'; 

$model = new ContractModel();

echo "--- BẮT ĐẦU CHẠY TIẾN TRÌNH HÀNG NGÀY (" . date('Y-m-d H:i:s') . ") ---\n";

// ==========================================
// PHẦN 1: GỬI MAIL NHẮC NHỞ (TRƯỚC 1 NGÀY)
// ==========================================
$expiringList = $model->getContractsExpiringTomorrow();
echo "-> Tìm thấy " . count($expiringList) . " hợp đồng sắp hết hạn.\n";

foreach ($expiringList as $item) {
    $to = $item['email'];
    $name = $item['full_name'];
    $room = $item['room_name'];
    $endDate = date('d/m/Y', strtotime($item['end_date']));

    $subject = "[KTX] Thông báo hết hạn hợp đồng";
    $message = "
        Xin chào $name,
        
        Đây là email tự động thông báo hợp đồng lưu trú KTX của bạn tại phòng $room (Tòa {$item['building']}) sẽ hết hạn vào ngày mai ($endDate).
        
        Hệ thống sẽ tự động làm thủ tục trả phòng cho bạn vào cuối ngày mai. 
        Vui lòng dọn dẹp tư trang và bàn giao lại phòng.
        
        Nếu muốn gia hạn, vui lòng liên hệ ban quản lý trước khi hết hạn.
        
        Trân trọng,
        Ban Quản Lý KTX.
    ";

    // Gửi mail (Sử dụng hàm mail() cơ bản hoặc PHPMailer)
    $headers = "From: quanlyktx@example.com" . "\r\n" .
               "Content-Type: text/plain; charset=UTF-8";

    if (mail($to, $subject, $message, $headers)) {
        echo "   [V] Đã gửi mail cho: $name ($to)\n";
    } else {
        echo "   [X] Lỗi gửi mail cho: $name ($to)\n";
    }
}

// ==========================================
// PHẦN 2: TỰ ĐỘNG XÓA SINH VIÊN (HẾT HẠN HÔM NAY)
// ==========================================
$processedCount = $model->processExpiredContracts();
echo "-> Đã tự động thanh lý và giải phóng giường cho $processedCount hợp đồng hết hạn.\n";

echo "--- KẾT THÚC TIẾN TRÌNH ---\n"; -->
