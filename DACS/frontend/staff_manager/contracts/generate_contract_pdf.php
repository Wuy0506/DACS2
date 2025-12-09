<?php
/**
 * Tạo file PDF hợp đồng lưu trú
 * 
 * Sử dụng:
 * - Mode 'display' (mặc định): Hiển thị PDF trên browser
 * - Mode 'return': Trả về PDF data để gửi email hoặc lưu file
 * - Mode 'email': Tạo PDF và gửi email cho sinh viên
 */

// Lấy tham số
$contract_id = isset($_GET['id']) ? intval($_GET['id']) : (isset($contract_id) ? $contract_id : 0);
$mode = isset($_GET['mode']) ? $_GET['mode'] : (isset($mode) ? $mode : 'display');

if ($contract_id <= 0) {
    if ($mode === 'return') {
        return ['success' => false, 'message' => 'ID hợp đồng không hợp lệ'];
    }
    die('ID hợp đồng không hợp lệ');
}

// Gọi trực tiếp model
require_once __DIR__ . '/../../../backend/model/staff/ContractModel.php';
$contractModel = new ContractModel();
$result = $contractModel->getContractById($contract_id);

if ($result['status'] !== 'success') {
    if ($mode === 'return') {
        return ['success' => false, 'message' => 'Không tìm thấy hợp đồng'];
    }
    die('Không tìm thấy hợp đồng: ' . $result['message']);
}

$contract = $result['data'];

// Tải thư viện TCPDF (nếu chưa có, cần tải về và đặt vào thư mục libs)
require_once __DIR__ . '/../../../tcpdf/tcpdf.php';

// Tạo PDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Thiết lập thông tin document
$pdf->SetCreator('DMS - Dormitory Management System');
$pdf->SetAuthor('DMS');
$pdf->SetTitle('Hợp đồng lưu trú #' . $contract['contract_id']);
$pdf->SetSubject('Hợp đồng lưu trú');

// Xóa header/footer mặc định
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Thiết lập font
$pdf->SetFont('dejavusans', '', 11);

// Thêm trang
$pdf->AddPage();

// Nội dung hợp đồng
$html = '
<style>
    h1 { text-align: center; color: #8B0000; font-size: 18px; }
    h2 { color: #333; font-size: 14px; margin-top: 15px; }
    .header { text-align: center; margin-bottom: 20px; }
    .info-table { width: 100%; border-collapse: collapse; margin: 10px 0; }
    .info-table td { padding: 5px; border: 1px solid #ddd; }
    .label { font-weight: bold; width: 40%; background-color: #f5f5f5; }
    .value { width: 60%; }
    .section { margin: 15px 0; }
    .signature { margin-top: 40px; }
    .signature-box { display: inline-block; width: 45%; text-align: center; }
    .terms { font-size: 10px; line-height: 1.6; }
</style>

<div class="header">
    <h1>CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</h1>
    <p style="text-align: center;"><b>Độc lập - Tự do - Hạnh phúc</b></p>
    <p style="text-align: center;">━━━━━━━━━━━━━━━━━━</p>
</div>

<h1>HỢP ĐỒNG LƯU TRÚ KÝ TÚC XÁ</h1>
<p style="text-align: center;"><i>Số: ' . str_pad($contract['contract_id'], 6, '0', STR_PAD_LEFT) . '/HĐLT</i></p>

<div class="section">
    <p><b>Căn cứ:</b></p>
    <ul>
        <li>Luật Dân sự năm 2015;</li>
        <li>Quy chế quản lý ký túc xá;</li>
        <li>Nhu cầu và khả năng của hai bên.</li>
    </ul>
</div>

<div class="section">
    <h2>ĐIỀU 1: CÁC BÊN THAM GIA HỢP ĐỒNG</h2>
    
    <p><b>BÊN CHO THUÊ (Bên A):</b></p>
    <table class="info-table">
        <tr>
            <td class="label">Tên đơn vị:</td>
            <td class="value">Ban Quản lý Ký túc xá</td>
        </tr>
        <tr>
            <td class="label">Địa chỉ:</td>
            <td class="value">Ký túc xá sinh viên</td>
        </tr>
        <tr>
            <td class="label">Người đại diện:</td>
            <td class="value">' . htmlspecialchars($contract['approved_by_name'] ?? 'Ban Giám đốc') . '</td>
        </tr>
    </table>
    
    <p><b>BÊN THUÊ (Bên B):</b></p>
    <table class="info-table">
        <tr>
            <td class="label">Họ và tên:</td>
            <td class="value">' . htmlspecialchars($contract['student_name']) . '</td>
        </tr>
        <tr>
            <td class="label">Mã sinh viên:</td>
            <td class="value">' . htmlspecialchars($contract['student_code'] ?? 'N/A') . '</td>
        </tr>
        <tr>
            <td class="label">Ngày sinh:</td>
            <td class="value">' . date('d/m/Y', strtotime($contract['date_of_birth'])) . '</td>
        </tr>
        <tr>
            <td class="label">Giới tính:</td>
            <td class="value">' . htmlspecialchars($contract['gender']) . '</td>
        </tr>
        <tr>
            <td class="label">Khoa:</td>
            <td class="value">' . htmlspecialchars($contract['faculty']) . '</td>
        </tr>
        <tr>
            <td class="label">Chuyên ngành:</td>
            <td class="value">' . htmlspecialchars($contract['major']) . '</td>
        </tr>
        <tr>
            <td class="label">Số điện thoại:</td>
            <td class="value">' . htmlspecialchars($contract['student_phone']) . '</td>
        </tr>
        <tr>
            <td class="label">Email:</td>
            <td class="value">' . htmlspecialchars($contract['student_email']) . '</td>
        </tr>
        <tr>
            <td class="label">Địa chỉ thường trú:</td>
            <td class="value">' . htmlspecialchars($contract['address']) . '</td>
        </tr>
    </table>
</div>

<div class="section">
    <h2>ĐIỀU 2: NỘI DUNG HỢP ĐỒNG</h2>
    
    <table class="info-table">
        <tr>
            <td class="label">Mã phòng:</td>
            <td class="value">' . htmlspecialchars($contract['room_id']) . '</td>
        </tr>
        <tr>
            <td class="label">Vị trí:</td>
            <td class="value">Tòa ' . htmlspecialchars($contract['building']) . ' - Tầng ' . $contract['floor'] . '</td>
        </tr>
        <tr>
            <td class="label">Loại phòng:</td>
            <td class="value">Phòng ' . $contract['capacity'] . ' người</td>
        </tr>
        <tr>
            <td class="label">Ngày bắt đầu:</td>
            <td class="value">' . date('d/m/Y', strtotime($contract['created_date'])) . '</td>
        </tr>
        <tr>
            <td class="label">Ngày kết thúc:</td>
            <td class="value">' . date('d/m/Y', strtotime($contract['end_date'])) . '</td>
        </tr>
        <tr>
            <td class="label">Giá thuê:</td>
            <td class="value"><b>' . number_format($contract['price_per_month'], 0, ',', '.') . ' VNĐ/tháng</b></td>
        </tr>
    </table>
</div>

<div class="section">
    <h2>ĐIỀU 3: QUYỀN VÀ NGHĨA VỤ CỦA BÊN A</h2>
    <div class="terms">
        <p><b>1. Quyền:</b></p>
        <ul>
            <li>Yêu cầu Bên B thanh toán đầy đủ, đúng hạn các khoản phí theo quy định.</li>
            <li>Kiểm tra, giám sát việc chấp hành nội quy của Bên B.</li>
            <li>Đơn phương chấm dứt hợp đồng nếu Bên B vi phạm nghiêm trọng nội quy.</li>
        </ul>
        
        <p><b>2. Nghĩa vụ:</b></p>
        <ul>
            <li>Bàn giao phòng ở đúng thời hạn, đảm bảo đầy đủ trang thiết bị.</li>
            <li>Bảo trì, sửa chữa các hư hỏng do hao mòn tự nhiên.</li>
            <li>Đảm bảo an ninh, trật tự trong khu ký túc xá.</li>
        </ul>
    </div>
</div>

<div class="section">
    <h2>ĐIỀU 4: QUYỀN VÀ NGHĨA VỤ CỦA BÊN B</h2>
    <div class="terms">
        <p><b>1. Quyền:</b></p>
        <ul>
            <li>Được sử dụng phòng và các tiện ích chung của ký túc xá.</li>
            <li>Được bảo vệ quyền lợi hợp pháp theo quy định.</li>
            <li>Được thông báo trước khi có thay đổi về giá thuê, nội quy.</li>
        </ul>
        
        <p><b>2. Nghĩa vụ:</b></p>
        <ul>
            <li>Thanh toán đầy đủ, đúng hạn các khoản phí theo quy định.</li>
            <li>Giữ gìn vệ sinh, trật tự, an ninh trong phòng và khu vực chung.</li>
            <li>Chấp hành nghiêm chỉnh nội quy ký túc xá.</li>
            <li>Bồi thường thiệt hại do mình gây ra.</li>
            <li>Thông báo trước 30 ngày nếu muốn chấm dứt hợp đồng.</li>
        </ul>
    </div>
</div>

<div class="section">
    <h2>ĐIỀU 5: ĐIỀU KHOẢN CHUNG</h2>
    <div class="terms">
        <ul>
            <li>Hợp đồng có hiệu lực kể từ ngày ký.</li>
            <li>Mọi tranh chấp phát sinh sẽ được giải quyết thông qua thương lượng, hòa giải.</li>
            <li>Hợp đồng được lập thành 02 bản có giá trị pháp lý như nhau, mỗi bên giữ 01 bản.</li>
        </ul>
    </div>
</div>

<div class="signature">
    <table style="width: 100%; margin-top: 30px;">
        <tr>
            <td style="width: 50%; text-align: center; vertical-align: top;">
                <p><b>ĐẠI DIỆN BÊN A</b></p>
                <p><i>(Ký, ghi rõ họ tên)</i></p>
                <br><br><br>
                <p>_____________________</p>
            </td>
            <td style="width: 50%; text-align: center; vertical-align: top;">
                <p><b>ĐẠI DIỆN BÊN B</b></p>
                <p><i>(Ký, ghi rõ họ tên)</i></p>
                <br><br><br>
                <p>' . htmlspecialchars($contract['student_name']) . '</p>
            </td>
        </tr>
    </table>
</div>

<p style="text-align: center; margin-top: 20px; font-size: 10px; color: #666;">
    <i>Ngày tạo hợp đồng: ' . date('d/m/Y', strtotime($contract['created_date'])) . '</i>
</p>
';

// Xuất HTML ra PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Xuất file PDF theo mode
$filename = 'HopDong_' . $contract['contract_id'] . '_' . date('Ymd') . '.pdf';

switch ($mode) {
    case 'return':
        // Trả về PDF data để sử dụng trong code
        return [
            'success' => true,
            'data' => $pdf->Output('', 'S'), // 'S' = return as string
            'filename' => $filename,
            'contract' => $contract
        ];
        
    case 'email':
        // Tạo PDF và gửi email
        require_once __DIR__ . '/../../../backend/service/EmailService.php';
        
        $pdfData = $pdf->Output('', 'S');
        $emailResult = EmailService::sendContractEmail(
            $contract['contract_id'],
            $contract['student_email'],
            $contract['student_name'],
            $pdfData
        );
        
        if ($emailResult['success']) {
            echo json_encode([
                'success' => true,
                'message' => 'Hợp đồng đã được gửi đến email: ' . $contract['student_email']
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi gửi email: ' . $emailResult['message']
            ]);
        }
        exit();
        
    case 'download':
        // Download file
        $pdf->Output($filename, 'D');
        break;
        
    case 'display':
    default:
        // Hiển thị trực tiếp trên browser (mặc định)
        $pdf->Output($filename, 'I'); // 'I' = inline display
        break;
}
?>