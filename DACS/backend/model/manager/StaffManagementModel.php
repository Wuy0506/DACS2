<?php
/**
 * Staff Management Model
 * Xử lý các chức năng quản lý nhân viên dành cho Manager
 */

require_once __DIR__ . '/../database.php';

class StaffManagementModel {
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
        
        if (!$this->conn) {
            die("Database connection failed");
        }
    }
    
    /**
     * Lấy danh sách tất cả nhân viên
     */
    public function getAllStaff($role = null) {
        $sql = "SELECT u.user_id, u.username, u.full_name, u.email, u.phone, u.role,
                s.position, s.hire_date
                FROM Users u
                LEFT JOIN Staff s ON u.user_id = s.staff_id
                WHERE u.role IN ('staff', 'manager')";
        
        if ($role && $role !== 'all') {
            $sql .= " AND u.role = ?";
        }
        
        $sql .= " ORDER BY u.user_id DESC";
        
        if ($role && $role !== 'all') {
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $role);
        } else {
            $stmt = $this->conn->prepare($sql);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $staffList = [];
        while ($row = $result->fetch_assoc()) {
            $staffList[] = $row;
        }
        
        return [
            'success' => true,
            'staff' => $staffList
        ];
    }
    
    /**
     * Lấy chi tiết nhân viên
     */
    public function getStaffDetails($userId) {
        $sql = "SELECT u.user_id, u.username, u.full_name, u.email, u.phone, u.role,
                s.position, s.hire_date,
                m.department
                FROM Users u
                LEFT JOIN Staff s ON u.user_id = s.staff_id
                LEFT JOIN Manager m ON u.user_id = m.manager_id
                WHERE u.user_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return [
                'success' => true,
                'staff' => $row
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Không tìm thấy nhân viên'
            ];
        }
    }
    
    /**
     * Thêm nhân viên mới
     */
    public function addStaff($data) {
        // Kiểm tra username đã tồn tại chưa
        $checkSql = "SELECT user_id FROM Users WHERE username = ?";
        $stmt = $this->conn->prepare($checkSql);
        $stmt->bind_param("s", $data['username']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return [
                'success' => false,
                'message' => 'Tên đăng nhập đã tồn tại'
            ];
        }
        
        // Bắt đầu transaction
        $this->conn->begin_transaction();
        
        try {
            // Thêm vào bảng Users
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            $sql = "INSERT INTO Users (username, password, full_name, email, phone, role)
                    VALUES (?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ssssss", 
                $data['username'],
                $hashedPassword,
                $data['full_name'],
                $data['email'],
                $data['phone'],
                $data['role']
            );
            
            if (!$stmt->execute()) {
                throw new Exception("Lỗi khi thêm vào bảng Users");
            }
            
            $userId = $this->conn->insert_id;
            
            // Thêm vào bảng Staff hoặc Manager tùy role
            if ($data['role'] === 'staff') {
                $sql = "INSERT INTO Staff (staff_id, position, hire_date)
                        VALUES (?, ?, ?)";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("iss", $userId, $data['position'], $data['hire_date']);
            } else if ($data['role'] === 'manager') {
                $sql = "INSERT INTO Manager (manager_id, department, hire_date)
                        VALUES (?, ?, ?)";
                $stmt = $this->conn->prepare($sql);
                $department = isset($data['department']) ? $data['department'] : '';
                $stmt->bind_param("iss", $userId, $department, $data['hire_date']);
            }
            
            if (!$stmt->execute()) {
                throw new Exception("Lỗi khi thêm vào bảng Staff/Manager");
            }
            
            $this->conn->commit();
            
            return [
                'success' => true,
                'message' => 'Thêm nhân viên thành công',
                'user_id' => $userId
            ];
            
        } catch (Exception $e) {
            $this->conn->rollback();
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Cập nhật thông tin nhân viên
     */
    public function updateStaff($userId, $data) {
        $this->conn->begin_transaction();
        
        try {
            // Cập nhật bảng Users
            $sql = "UPDATE Users SET full_name = ?, email = ?, phone = ?";
            $params = [$data['full_name'], $data['email'], $data['phone']];
            $types = "sss";
            
            // Nếu có password mới
            if (!empty($data['password'])) {
                $sql .= ", password = ?";
                $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
                $params[] = $hashedPassword;
                $types .= "s";
            }
            
            $sql .= " WHERE user_id = ?";
            $params[] = $userId;
            $types .= "i";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            
            if (!$stmt->execute()) {
                throw new Exception("Lỗi khi cập nhật bảng Users");
            }
            
            // Lấy role của user
            $roleSql = "SELECT role FROM Users WHERE user_id = ?";
            $stmt = $this->conn->prepare($roleSql);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $role = $row['role'];
            
            // Cập nhật bảng Staff hoặc Manager
            if ($role === 'staff' && isset($data['position'])) {
                $sql = "UPDATE Staff SET position = ? WHERE staff_id = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("si", $data['position'], $userId);
                $stmt->execute();
            } else if ($role === 'manager' && isset($data['department'])) {
                $sql = "UPDATE Manager SET department = ? WHERE manager_id = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("si", $data['department'], $userId);
                $stmt->execute();
            }
            
            $this->conn->commit();
            
            return [
                'success' => true,
                'message' => 'Cập nhật nhân viên thành công'
            ];
            
        } catch (Exception $e) {
            $this->conn->rollback();
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Xóa nhân viên
     */
    public function deleteStaff($userId) {
        // Kiểm tra xem có phải manager đang đăng nhập không
        $sql = "DELETE FROM Users WHERE user_id = ? AND role IN ('staff', 'manager')";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                return [
                    'success' => true,
                    'message' => 'Xóa nhân viên thành công'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Không thể xóa nhân viên này'
                ];
            }
        } else {
            return [
                'success' => false,
                'message' => 'Lỗi khi xóa nhân viên: ' . $stmt->error
            ];
        }
    }
    
    /**
     * Lấy thống kê nhân viên
     */
    public function getStaffStatistics() {
        $sql = "SELECT 
                COUNT(*) as total_staff,
                SUM(CASE WHEN u.role = 'staff' THEN 1 ELSE 0 END) as total_regular_staff,
                SUM(CASE WHEN u.role = 'manager' THEN 1 ELSE 0 END) as total_managers
                FROM Users u
                WHERE u.role IN ('staff', 'manager')";
        
        $result = $this->conn->query($sql);
        
        if ($row = $result->fetch_assoc()) {
            return [
                'success' => true,
                'stats' => $row
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Không thể lấy thống kê'
            ];
        }
    }
}
?>
