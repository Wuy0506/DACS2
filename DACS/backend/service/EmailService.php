<?php
/**
 * EmailService - Xử lý gửi email
 */

// Import PHPMailer classes
require_once __DIR__ . '/../../PHPMailer-master/src/Exception.php';
require_once __DIR__ . '/../../PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/../../PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    private static $EMAIL = "nguyencongtuekhang@gmail.com";
    private static $PASSWORD = "xgmxxvxphpxqyxcz";
    private static $FROM_NAME = "Ký Túc Xá - DMS";
    
    /**
     * Gửi email với file đính kèm
     * 
     * @param string $to Email người nhận
     * @param string $toName Tên người nhận
     * @param string $subject Tiêu đề email
     * @param string $body Nội dung email (HTML)
     * @param string $attachmentPath Đường dẫn file đính kèm (optional)
     * @param string $attachmentName Tên file đính kèm (optional)
     * @param string $attachmentData Binary data của file (optional - ưu tiên hơn path)
     * @return array ['success' => bool, 'message' => string]
     */
    public static function sendEmail($to, $toName, $subject, $body, $attachmentPath = null, $attachmentName = null, $attachmentData = null) {
        try {
            // Sử dụng PHPMailer
            return self::sendWithPHPMailer($to, $toName, $subject, $body, $attachmentPath, $attachmentName, $attachmentData);
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Lỗi gửi email: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Gửi email sử dụng PHPMailer
     */
    private static function sendWithPHPMailer($to, $toName, $subject, $body, $attachmentPath, $attachmentName, $attachmentData) {
        $mail = new PHPMailer(true);
        
        try {
            // Cấu hình SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = self::$EMAIL;
            $mail->Password = self::$PASSWORD;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->CharSet = 'UTF-8';
            
            // Người gửi
            $mail->setFrom(self::$EMAIL, self::$FROM_NAME);
            
            // Người nhận
            $mail->addAddress($to, $toName);
            
            // Nội dung email
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = strip_tags($body);
            
            // File đính kèm
            if ($attachmentData) {
                // Đính kèm từ binary data (ưu tiên)
                $mail->addStringAttachment($attachmentData, $attachmentName);
            } elseif ($attachmentPath && file_exists($attachmentPath)) {
                // Đính kèm từ file
                $mail->addAttachment($attachmentPath, $attachmentName);
            }
            
            // Gửi email
            $mail->send();
            
            return [
                'success' => true,
                'message' => 'Email đã được gửi thành công'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Lỗi PHPMailer: ' . $mail->ErrorInfo
            ];
        }
    }
    
    /**
     * Gửi hợp đồng qua email
     * 
     * @param int $contractId ID hợp đồng
     * @param string $studentEmail Email sinh viên
     * @param string $studentName Tên sinh viên
     * @param string $pdfData Binary data của PDF
     * @return array
     */
    public static function sendContractEmail($contractId, $studentEmail, $studentName, $pdfData) {
        $subject = "Hợp đồng lưu trú ký túc xá - Số " . str_pad($contractId, 6, '0', STR_PAD_LEFT);
        
        $body = '
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
                .content { background: #f9f9f9; padding: 20px; border: 1px solid #ddd; border-top: none; }
                .footer { background: #333; color: white; padding: 15px; text-align: center; font-size: 12px; border-radius: 0 0 5px 5px; }
                .button { display: inline-block; padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin: 10px 0; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2>🏢 KÝ TÚC XÁ - DMS</h2>
                    <p>Dormitory Management System</p>
                </div>
                <div class="content">
                    <h3>Kính gửi: ' . htmlspecialchars($studentName) . '</h3>
                    <p>Chúng tôi xin gửi đến bạn <strong>Hợp đồng lưu trú</strong> với các thông tin sau:</p>
                    <ul>
                        <li><strong>Mã hợp đồng:</strong> #' . str_pad($contractId, 6, '0', STR_PAD_LEFT) . '</li>
                        <li><strong>Ngày gửi:</strong> ' . date('d/m/Y H:i:s') . '</li>
                    </ul>
                    <p>Vui lòng kiểm tra file đính kèm để xem chi tiết hợp đồng.</p>
                    <p><strong>Lưu ý:</strong></p>
                    <ul>
                        <li>Vui lòng đọc kỹ các điều khoản trong hợp đồng</li>
                        <li>Thanh toán đúng hạn theo quy định</li>
                        <li>Liên hệ Ban Quản lý nếu có thắc mắc</li>
                    </ul>
                    <p>Trân trọng,<br><strong>Ban Quản lý Ký túc xá</strong></p>
                </div>
                <div class="footer">
                    <p>Email: ' . self::$EMAIL . '</p>
                    <p>© ' . date('Y') . ' DMS - Dormitory Management System. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ';
        
        $fileName = 'HopDong_' . $contractId . '_' . date('Ymd') . '.pdf';
        
        return self::sendEmail($studentEmail, $studentName, $subject, $body, null, $fileName, $pdfData);
    }
}
?>