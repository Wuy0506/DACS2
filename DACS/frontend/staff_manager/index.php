<?php
// Kiểm tra nếu đã login qua PHP session
session_start();
if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true) {
    // Kiểm tra role - chỉ staff và manager mới được vào
    if (isset($_SESSION['user']) && 
        ($_SESSION['user']['role'] === 'staff' || $_SESSION['user']['role'] === 'manager')) {
        // Đã login rồi, chuyển đến dashboard
        header('Location: dashboard.php');
        exit();
    }
}

// Chuyển hướng đến trang login trong LoginDarkSunSet
header('Location: ../../LoginDarkSunSet/login.php');
exit();
?>
