<?php
include_once __DIR__ . '/../../model/class/User.php';
include_once __DIR__ . '/../database.php';

class UserModel  {
  private $conn;
  function __construct() {
    global $conn;
    $this->conn = $conn;
  }
    public function register($username, $password, $full_name, $email, $phone, $role = 'student') {
        $sql = "SELECT * FROM Users WHERE username = '$username'";
        $result = mysqli_query($this->conn, $sql);
        
        if (mysqli_num_rows($result) > 0) {
            return [
                'success' => false,
                'message' => 'Tên đăng nhập đã tồn tại'
            ];
        }
 
        $sql = "SELECT * FROM Users WHERE email = '$email'";
        $result = mysqli_query($this->conn, $sql);
        
        if (mysqli_num_rows($result) > 0) {
            return [
                'success' => false,
                'message' => 'Email đã được sử dụng'
            ];
        }
        
        // Mã hóa mật khẩu
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Thêm người dùng mới
        $sql = "INSERT INTO Users(username, password, full_name, email, phone, role) 
                VALUES('$username', '$hashedPassword', '$full_name', '$email', '$phone', '$role')";
        
        $result = mysqli_query($this->conn, $sql);
        
        if ($result) {
            $userId = mysqli_insert_id($this->conn);
            
            // Trigger 'after_user_insert' sẽ tự động tạo record trong Student/Staff/Manager
            // Không cần xử lý thủ công trong PHP nữa
            
            return [
                'success' => true,
                'message' => 'Đăng ký thành công',
                'user_id' => $userId
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Đăng ký thất bại: ' . mysqli_error($this->conn)
            ];
        }
    }
    
    /**
     * Đăng nhập người dùng
     */
    public function login($username, $password) {
        // Tìm người dùng theo username
        $sql = "SELECT * FROM Users WHERE username = '$username' OR email = '$username'";
        $result = mysqli_query($this->conn, $sql);
        
        if (mysqli_num_rows($result) == 0) {
            return [
                'success' => false,
                'message' => 'Tên đăng nhập hoặc email không tồn tại'
            ];
        }
        
        $user = mysqli_fetch_assoc($result);
        
        // Kiểm tra mật khẩu
        if (password_verify($password, $user['password'])) {
            // Loại bỏ mật khẩu khỏi dữ liệu trả về
            unset($user['password']);
            
            return [
                'success' => true,
                'message' => 'Đăng nhập thành công',
                'user' => $user
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Mật khẩu không chính xác'
            ];
        }
    }
    
    /**
     * Lấy thông tin người dùng theo ID
     */
    public function getUserById($userId) {
        $sql = "SELECT * FROM Users WHERE user_id = '$userId'";
        $result = mysqli_query($this->conn, $sql);
        
        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            unset($user['password']); // Loại bỏ mật khẩu
            return $user;
        }
        
        return null;
    }
    
    /**
     * Cập nhật thông tin người dùng
     */
    public function updateUser($userId, $data) {
        // Không cho phép cập nhật mật khẩu qua hàm này
        if (isset($data['password'])) {
            unset($data['password']);
        }
        
        $updateValues = [];
        foreach ($data as $key => $value) {
            $updateValues[] = "$key = '$value'";
        }
        
        $updateString = implode(", ", $updateValues);
        
        $sql = "UPDATE Users SET $updateString WHERE user_id = '$userId'";
        $result = mysqli_query($this->conn, $sql);
        
        return $result ? true : false;
    }
    
    /**
     * Cập nhật mật khẩu
     */
    public function updatePassword($userId, $currentPassword, $newPassword) {
        // Lấy thông tin người dùng
        $sql = "SELECT * FROM Users WHERE user_id = '$userId'";
        $result = mysqli_query($this->conn, $sql);
        
        if (mysqli_num_rows($result) == 0) {
            return [
                'success' => false,
                'message' => 'Không tìm thấy người dùng'
            ];
        }
        
        $user = mysqli_fetch_assoc($result);
        
        // Kiểm tra mật khẩu hiện tại
        if (!password_verify($currentPassword, $user['password'])) {
            return [
                'success' => false,
                'message' => 'Mật khẩu hiện tại không chính xác'
            ];
        }
        
        // Mã hóa mật khẩu mới
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        // Cập nhật mật khẩu
        $sql = "UPDATE Users SET password = '$hashedPassword' WHERE user_id = '$userId'";
        $result = mysqli_query($this->conn, $sql);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'Cập nhật mật khẩu thành công'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Cập nhật mật khẩu thất bại: ' . mysqli_error($this->conn)
            ];
        }
    }
}
?>