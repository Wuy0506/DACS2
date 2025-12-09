<?php 
// Thông tin kết nối database
$host = "localhost";
$username = "root";
$password = ""; // Thường XAMPP sử dụng mật khẩu trống
$database = "managerktx";
$port = 3307;

// Tạo kết nối
try {
    $conn = mysqli_connect($host, $username, $password, $database,$port);
    
    // Kiểm tra kết nối
    if (!$conn) {
        die("Lỗi kết nối cơ sở dữ liệu: " . mysqli_connect_error());
    }
    
    // Thiết lập charset
    mysqli_set_charset($conn, "utf8");
} catch (Exception $e) {
    die("Lỗi kết nối: " . $e->getMessage());
}
?>