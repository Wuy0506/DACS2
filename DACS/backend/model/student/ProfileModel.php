<?php
include_once __DIR__ . '/../database.php';

class ProfileModel {
    private $conn;
    
    function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    /**
     * Lấy thông tin profile đầy đủ của user
     */
    public function getUserProfile($userId) {
        $sql = "SELECT 
                    u.user_id,
                    u.username,
                    u.full_name,
                    u.email,
                    u.phone,
                    u.role,
                    s.student_id,
                    s.faculty,
                    s.major,
                    s.gender,
                    s.date_of_birth,
                    s.address
                FROM Users u
                LEFT JOIN Student s ON u.user_id = s.user_id
                WHERE u.user_id = '$userId'";
        
        $result = mysqli_query($this->conn, $sql);
        
        if (!$result) {
            return [
                'success' => false,
                'message' => 'Lỗi truy vấn: ' . mysqli_error($this->conn)
            ];
        }
        
        $data = mysqli_fetch_assoc($result);
        
        if (!$data) {
            return [
                'success' => false,
                'message' => 'Không tìm thấy thông tin người dùng'
            ];
        }
        
        return [
            'success' => true,
            'data' => [
                'user_id' => $data['user_id'],
                'username' => $data['username'],
                'full_name' => $data['full_name'] ?? '',
                'email' => $data['email'] ?? '',
                'phone' => $data['phone'] ?? '',
                'role' => $data['role'] ?? '',
                'student_id' => $data['student_id'] ?? '',
                'faculty' => $data['faculty'] ?? '',
                'major' => $data['major'] ?? '',
                'gender' => $data['gender'] ?? '',
                'date_of_birth' => $data['date_of_birth'] ?? '',
                'address' => $data['address'] ?? ''
            ]
        ];
    }
    
    /**
     * Cập nhật thông tin profile
     */
    public function updateProfile($userId, $profileData) {
        // Bước 1: Cập nhật bảng User
        $userUpdates = [];
        
        if (isset($profileData['full_name']) && !empty(trim($profileData['full_name']))) {
            $fullName = mysqli_real_escape_string($this->conn, trim($profileData['full_name']));
            $userUpdates[] = "full_name = '$fullName'";
        }
        
        if (isset($profileData['email']) && !empty(trim($profileData['email']))) {
            // Kiểm tra email đã tồn tại chưa (trừ user hiện tại)
            $email = mysqli_real_escape_string($this->conn, trim($profileData['email']));
            $checkEmailSql = "SELECT user_id FROM Users WHERE email = '$email' AND user_id != '$userId'";
            $checkResult = mysqli_query($this->conn, $checkEmailSql);
if (mysqli_num_rows($checkResult) > 0) {
                return [
                    'success' => false,
                    'message' => 'Email đã được sử dụng bởi tài khoản khác'
                ];
            }
            
            $userUpdates[] = "email = '$email'";
        }
        
        if (isset($profileData['phone']) && !empty(trim($profileData['phone']))) {
            $phone = mysqli_real_escape_string($this->conn, trim($profileData['phone']));
            $userUpdates[] = "phone = '$phone'";
        }
        
        // Thực hiện UPDATE User nếu có thay đổi
        if (!empty($userUpdates)) {
            $updateUserSql = "UPDATE Users SET " . implode(', ', $userUpdates) . " WHERE user_id = '$userId'";
            $updateResult = mysqli_query($this->conn, $updateUserSql);
            
            if (!$updateResult) {
                return [
                    'success' => false,
                    'message' => 'Lỗi cập nhật thông tin User: ' . mysqli_error($this->conn)
                ];
            }
        }
        
        // Bước 2: Cập nhật hoặc tạo mới bảng Student (nếu role là student)
        $checkRoleSql = "SELECT role FROM Users WHERE user_id = '$userId'";
        $roleResult = mysqli_query($this->conn, $checkRoleSql);
        $roleData = mysqli_fetch_assoc($roleResult);
        
        if ($roleData && $roleData['role'] === 'student') {
            // Chuẩn bị dữ liệu Student
            $studentUpdates = [];
            
            if (isset($profileData['student_id']) && !empty(trim($profileData['student_id']))) {
                $studentId = mysqli_real_escape_string($this->conn, trim($profileData['student_id']));
                $studentUpdates['student_id'] = $studentId;
            }
            
            if (isset($profileData['faculty']) && !empty(trim($profileData['faculty']))) {
                $faculty = mysqli_real_escape_string($this->conn, trim($profileData['faculty']));
                $studentUpdates['faculty'] = $faculty;
            }
            
            if (isset($profileData['major']) && !empty(trim($profileData['major']))) {
                $major = mysqli_real_escape_string($this->conn, trim($profileData['major']));
                $studentUpdates['major'] = $major;
            }
            
            if (isset($profileData['gender']) && !empty(trim($profileData['gender']))) {
                $gender = mysqli_real_escape_string($this->conn, trim($profileData['gender']));
                $studentUpdates['gender'] = $gender;
            }
            
            if (isset($profileData['date_of_birth']) && !empty(trim($profileData['date_of_birth']))) {
                $dateOfBirth = mysqli_real_escape_string($this->conn, trim($profileData['date_of_birth']));
                $studentUpdates['date_of_birth'] = $dateOfBirth;
            }
if (isset($profileData['address']) && !empty(trim($profileData['address']))) {
                $address = mysqli_real_escape_string($this->conn, trim($profileData['address']));
                $studentUpdates['address'] = $address;
            }
            
            // Kiểm tra student_id đã có trong Student chưa
            if (!empty($studentUpdates)) {
                $checkStudentSql = "SELECT user_id FROM Student WHERE user_id  = '$userId'";
                $checkStudentResult = mysqli_query($this->conn, $checkStudentSql);
                
                if (mysqli_num_rows($checkStudentResult) > 0) {
                    // Đã có → UPDATE
                    $updateParts = [];
                    foreach ($studentUpdates as $field => $value) {
                        $updateParts[] = "$field = '$value'";
                    }
                    $updateStudentSql = "UPDATE Student SET " . implode(', ', $updateParts) . " WHERE user_id = '$userId'";
                    mysqli_query($this->conn, $updateStudentSql);
                } else {
                    // Chưa có → INSERT
                    $fields = ['user_id '];
                    $values = ["'$userId'"];
                    
                    foreach ($studentUpdates as $field => $value) {
                        $fields[] = $field;
                        $values[] = "'$value'";
                    }
                    
                    $insertStudentSql = "INSERT INTO Student (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ")";
                    mysqli_query($this->conn, $insertStudentSql);
                }
            }
        }
        
        return [
            'success' => true,
            'message' => 'Cập nhật thông tin thành công'
        ];
    }
    
    /**
     * Kiểm tra xem profile của student đã hoàn thành chưa
     */
    public function checkProfileComplete($userId) {
        $sql = "SELECT 
                    u.user_id,
                    u.full_name,
                    u.email,
                    u.phone,
                    u.role,
                    s.student_id,
                    s.faculty,
                    s.major,
                    s.gender,
                    s.date_of_birth,
                    s.address
                FROM Users u
                LEFT JOIN Student s ON u.user_id = s.user_id
                WHERE u.user_id = '$userId'";
        
        $result = mysqli_query($this->conn, $sql);
        
        if (!$result) {
            return [
                'success' => false,
                'message' => 'Lỗi truy vấn: ' . mysqli_error($this->conn)
            ];
        }
        
        $data = mysqli_fetch_assoc($result);
        
        if (!$data) {
            return [
                'success' => false,
                'message' => 'Không tìm thấy thông tin người dùng'
            ];
        }
// Nếu không phải student, cho phép đăng ký
        if ($data['role'] !== 'student') {
            return [
                'success' => true,
                'is_complete' => true,
                'missing_fields' => []
            ];
        }
        
        // Kiểm tra các trường bắt buộc cho student
        $requiredFields = [
            'full_name' => 'Họ và Tên',
            'email' => 'Email',
            'phone' => 'Số điện thoại',
            'student_id' => 'Mã sinh viên',
            'faculty' => 'Khoa',
            'major' => 'Ngành',
            'gender' => 'Giới tính',
            'date_of_birth' => 'Ngày sinh',
            'address' => 'Địa chỉ'
        ];
        
        $missingFields = [];
        
        foreach ($requiredFields as $field => $label) {
            if (empty($data[$field]) || trim($data[$field]) === '') {
                $missingFields[] = $label;
            }
        }
        
        $isComplete = empty($missingFields);
        
        return [
            'success' => true,
            'is_complete' => $isComplete,
            'missing_fields' => $missingFields
        ];
    }
    
    /**
     * Đổi mật khẩu
     */
    public function changePassword($userId, $oldPassword, $newPassword) {
        // Kiểm tra mật khẩu cũ
        $checkSql = "SELECT password FROM Users WHERE user_id = '$userId'";
        $result = mysqli_query($this->conn, $checkSql);
        $user = mysqli_fetch_assoc($result);
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Không tìm thấy người dùng'
            ];
        }
        
        // Verify old password (giả sử password được hash)
        // Nếu password không hash thì so sánh trực tiếp
        if ($user['password'] !== $oldPassword) {
            return [
                'success' => false,
                'message' => 'Mật khẩu cũ không đúng'
            ];
        }
        
        // Cập nhật mật khẩu mới
        $newPasswordEscaped = mysqli_real_escape_string($this->conn, $newPassword);
        $updateSql = "UPDATE Users SET password = '$newPasswordEscaped' WHERE user_id = '$userId'";
        
        if (mysqli_query($this->conn, $updateSql)) {
            return [
                'success' => true,
                'message' => 'Đổi mật khẩu thành công'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Lỗi cập nhật mật khẩu: ' . mysqli_error($this->conn)
            ];
        }
    }
}
?>
