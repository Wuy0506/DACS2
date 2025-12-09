<?php
/**
 * In hợp đồng (có thể in ra PDF từ trình duyệt)
 */

// Lấy ID hợp đồng từ URL
$contract_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($contract_id <= 0) {
    die('ID hợp đồng không hợp lệ');
}

// Gọi trực tiếp model
require_once __DIR__ . '/../../../backend/model/staff/ContractModel.php';
$contractModel = new ContractModel();
$result = $contractModel->getContractById($contract_id);

if ($result['status'] !== 'success') {
    die('Không tìm thấy hợp đồng: ' . $result['message']);
}

$contract = $result['data'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hợp đồng lưu trú #<?php echo $contract['contract_id']; ?></title>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; }
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            line-height: 1.6;
            max-width: 210mm;
            margin: 0 auto;
            padding: 20mm;
            background: white;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .header h1 {
            margin: 5px 0;
            font-size: 16px;
            font-weight: bold;
        }
        
        .header p {
            margin: 3px 0;
        }
        
        .title {
            text-align: center;
            margin: 30px 0;
        }
        
        .title h2 {
            font-size: 20px;
            color: #8B0000;
            margin: 10px 0;
        }
        
        .section {
            margin: 20px 0;
        }
        
        .section h3 {
            font-size: 14px;
            font-weight: bold;
            margin: 15px 0 10px 0;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        
        .info-table td {
            padding: 8px;
            border: 1px solid #333;
        }
        
        .info-table .label {
            font-weight: bold;
            width: 40%;
            background-color: #f5f5f5;
        }
        
        .info-table .value {
            width: 60%;
        }
        
        .terms {
            font-size: 13px;
        }
        
        .terms ul {
            margin: 5px 0;
            padding-left: 25px;
        }
        
        .terms li {
            margin: 5px 0;
        }
        
        .signature {
            margin-top: 50px;
        }
        
        .signature-row {
            display: flex;
            justify-content: space-between;
        }
        
        .signature-box {
            width: 45%;
            text-align: center;
        }
        
        .signature-box p {
            margin: 5px 0;
        }
        
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #333;
            display: inline-block;
            width: 200px;
        }
        
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .print-btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">
        🖨️ In / Lưu PDF
    </button>

    <div class="header">
        <h1>CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</h1>
        <p><strong>Độc lập - Tự do - Hạnh phúc</strong></p>
        <p>━━━━━━━━━━━━━━━━━━</p>
    </div>

    <div class="title">
        <h2>HỢP ĐỒNG LƯU TRÚ KÝ TÚC XÁ</h2>
        <p><em>Số: <?php echo str_pad($contract['contract_id'], 6, '0', STR_PAD_LEFT); ?>/HĐLT</em></p>
    </div>

    <div class="section">
        <p><strong>Căn cứ:</strong></p>
        <ul>
            <li>Luật Dân sự năm 2015;</li>
            <li>Quy chế quản lý ký túc xá;</li>
            <li>Nhu cầu và khả năng của hai bên.</li>
        </ul>
    </div>

    <div class="section">
        <h3>ĐIỀU 1: CÁC BÊN THAM GIA HỢP ĐỒNG</h3>
        
        <p><strong>BÊN CHO THUÊ (Bên A):</strong></p>
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
                <td class="value"><?php echo htmlspecialchars($contract['approved_by_name'] ?? 'Ban Giám đốc'); ?></td>
            </tr>
        </table>
        
        <p><strong>BÊN THUÊ (Bên B):</strong></p>
        <table class="info-table">
            <tr>
                <td class="label">Họ và tên:</td>
                <td class="value"><?php echo htmlspecialchars($contract['student_name']); ?></td>
            </tr>
            <tr>
                <td class="label">Mã sinh viên:</td>
                <td class="value"><?php echo htmlspecialchars($contract['student_code'] ?? 'N/A'); ?></td>
            </tr>
            <tr>
                <td class="label">Ngày sinh:</td>
                <td class="value"><?php echo date('d/m/Y', strtotime($contract['date_of_birth'])); ?></td>
            </tr>
            <tr>
                <td class="label">Giới tính:</td>
                <td class="value"><?php echo htmlspecialchars($contract['gender']); ?></td>
            </tr>
            <tr>
                <td class="label">Khoa:</td>
                <td class="value"><?php echo htmlspecialchars($contract['faculty']); ?></td>
            </tr>
            <tr>
                <td class="label">Chuyên ngành:</td>
                <td class="value"><?php echo htmlspecialchars($contract['major']); ?></td>
            </tr>
            <tr>
                <td class="label">Số điện thoại:</td>
                <td class="value"><?php echo htmlspecialchars($contract['student_phone']); ?></td>
            </tr>
            <tr>
                <td class="label">Email:</td>
                <td class="value"><?php echo htmlspecialchars($contract['student_email']); ?></td>
            </tr>
            <tr>
                <td class="label">Địa chỉ thường trú:</td>
                <td class="value"><?php echo htmlspecialchars($contract['address']); ?></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h3>ĐIỀU 2: NỘI DUNG HỢP ĐỒNG</h3>
        
        <table class="info-table">
            <tr>
                <td class="label">Mã phòng:</td>
                <td class="value"><?php echo htmlspecialchars($contract['room_id']); ?></td>
            </tr>
            <tr>
                <td class="label">Vị trí:</td>
                <td class="value">Tòa <?php echo htmlspecialchars($contract['building']); ?> - Tầng <?php echo $contract['floor']; ?></td>
            </tr>
            <tr>
                <td class="label">Loại phòng:</td>
                <td class="value">Phòng <?php echo $contract['capacity']; ?> người</td>
            </tr>
            <tr>
                <td class="label">Ngày bắt đầu:</td>
                <td class="value"><?php echo date('d/m/Y', strtotime($contract['start_date'])); ?></td>
            </tr>
            <tr>
                <td class="label">Ngày kết thúc:</td>
                <td class="value"><?php echo date('d/m/Y', strtotime($contract['registration_end_date'])); ?></td>
            </tr>
            <tr>
                <td class="label">Giá thuê:</td>
                <td class="value"><strong><?php echo number_format($contract['price_per_month'], 0, ',', '.'); ?> VNĐ/tháng</strong></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h3>ĐIỀU 3: QUYỀN VÀ NGHĨA VỤ CỦA BÊN A</h3>
        <div class="terms">
            <p><strong>1. Quyền:</strong></p>
            <ul>
                <li>Yêu cầu Bên B thanh toán đầy đủ, đúng hạn các khoản phí theo quy định.</li>
                <li>Kiểm tra, giám sát việc chấp hành nội quy của Bên B.</li>
                <li>Đơn phương chấm dứt hợp đồng nếu Bên B vi phạm nghiêm trọng nội quy.</li>
            </ul>
            
            <p><strong>2. Nghĩa vụ:</strong></p>
            <ul>
                <li>Bàn giao phòng ở đúng thời hạn, đảm bảo đầy đủ trang thiết bị.</li>
                <li>Bảo trì, sửa chữa các hư hỏng do hao mòn tự nhiên.</li>
                <li>Đảm bảo an ninh, trật tự trong khu ký túc xá.</li>
            </ul>
        </div>
    </div>

    <div class="section">
        <h3>ĐIỀU 4: QUYỀN VÀ NGHĨA VỤ CỦA BÊN B</h3>
        <div class="terms">
            <p><strong>1. Quyền:</strong></p>
            <ul>
                <li>Được sử dụng phòng và các tiện ích chung của ký túc xá.</li>
                <li>Được bảo vệ quyền lợi hợp pháp theo quy định.</li>
                <li>Được thông báo trước khi có thay đổi về giá thuê, nội quy.</li>
            </ul>
            
            <p><strong>2. Nghĩa vụ:</strong></p>
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
        <h3>ĐIỀU 5: ĐIỀU KHOẢN CHUNG</h3>
        <div class="terms">
            <ul>
                <li>Hợp đồng có hiệu lực kể từ ngày ký.</li>
                <li>Mọi tranh chấp phát sinh sẽ được giải quyết thông qua thương lượng, hòa giải.</li>
                <li>Hợp đồng được lập thành 02 bản có giá trị pháp lý như nhau, mỗi bên giữ 01 bản.</li>
            </ul>
        </div>
    </div>

    <div class="signature">
        <div class="signature-row">
            <div class="signature-box">
                <p><strong>ĐẠI DIỆN BÊN A</strong></p>
                <p><em>(Ký, ghi rõ họ tên)</em></p>
                <div class="signature-line"></div>
            </div>
            <div class="signature-box">
                <p><strong>ĐẠI DIỆN BÊN B</strong></p>
                <p><em>(Ký, ghi rõ họ tên)</em></p>
                <div class="signature-line"></div>
                <p><?php echo htmlspecialchars($contract['student_name']); ?></p>
            </div>
        </div>
    </div>

    <p style="text-align: center; margin-top: 30px; font-size: 12px; color: #666;">
        <em>Ngày tạo hợp đồng: <?php echo date('d/m/Y', strtotime($contract['created_date'])); ?></em>
    </p>
</body>
</html>
