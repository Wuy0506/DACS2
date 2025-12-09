<?php
// backend/permission.php

/**
 * Hàm kiểm tra quyền truy cập
 * @param array $allowedRoles Danh sách các role được phép (VD: ['manager', 'staff']). 
 * Để trống [] nghĩa là chỉ cần đăng nhập là được.
 */
function checkPermission($allowedRoles = []) {
    // 1. Khởi động session nếu chưa có
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // 2. Kiểm tra đã đăng nhập chưa
    if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
        // Chưa đăng nhập -> Đá về trang login
        header("Location: /LoginDarkSunSet/login.php");
        exit(); // QUAN TRỌNG: Dừng code ngay lập tức
    }

    // 3. Nếu mảng $allowedRoles trống -> Chỉ cần đăng nhập là đủ -> Pass
    if (empty($allowedRoles)) {
        return;
    }

    // 4. Kiểm tra Role
    // Lấy role hiện tại từ session (đảm bảo khớp với key bạn lưu lúc login)
    $currentRole = $_SESSION['user']['role'] ?? '';

    echo '<!-- Current Role: $currentRole -->'; // Debug: Hiển thị role hiện tại

    // Nếu role hiện tại không nằm trong danh sách cho phép
    if (!in_array($currentRole, $allowedRoles)) {
        
        // Không có quyền -> Báo lỗi và đá về trang chủ
        echo '<!DOCTYPE html><html lang="vi"><head><meta charset="utf-8"><title>Không có quyền</title>' .
         '<meta name="viewport" content="width=device-width, initial-scale=1"></head><body style="font-family: Arial, sans-serif; padding:40px;">' .
         '<h2 style="color:#c0392b;">Bạn không có quyền truy cập</h2>' .
         '<p>Trang này chỉ dành cho tài khoản riêng.</p>' .
         '<p><a href="/LoginDarkSunSet/login.php">Đăng nhập</a></p></body></html>';
        exit(); // Dừng code ngay
    }
}
?>