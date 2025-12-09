<?php
/**
 * System Settings Model
 * Xử lý các chức năng cấu hình hệ thống dành cho Manager
 */

require_once __DIR__ . '/../database.php';

class SystemSettingsModel {
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
        
        if (!$this->conn) {
            die("Database connection failed");
        }
    }
    
    /**
     * Lấy tất cả cấu hình hệ thống
     */
    public function getAllSettings() {
        $sql = "SELECT ss.*, u.full_name as updated_by_name
                FROM SystemSettings ss
                LEFT JOIN Users u ON ss.updated_by = u.user_id
                ORDER BY ss.setting_id";
        
        $result = $this->conn->query($sql);
        
        $settings = [];
        while ($row = $result->fetch_assoc()) {
            $settings[] = $row;
        }
        
        return [
            'success' => true,
            'settings' => $settings
        ];
    }
    
    /**
     * Lấy chi tiết một cấu hình
     */
    public function getSettingById($settingId) {
        $sql = "SELECT ss.*, u.full_name as updated_by_name
                FROM SystemSettings ss
                LEFT JOIN Users u ON ss.updated_by = u.user_id
                WHERE ss.setting_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $settingId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return [
                'success' => true,
                'setting' => $row
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Không tìm thấy cấu hình'
            ];
        }
    }
    
    /**
     * Lấy giá trị cấu hình theo tên
     */
    public function getSettingByName($settingName) {
        $sql = "SELECT * FROM SystemSettings WHERE setting_name = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $settingName);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return [
                'success' => true,
                'setting' => $row
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Không tìm thấy cấu hình'
            ];
        }
    }
    
    /**
     * Thêm cấu hình mới
     */
    public function addSetting($data, $managerId) {
        // Kiểm tra tên setting đã tồn tại chưa
        $checkSql = "SELECT setting_id FROM SystemSettings WHERE setting_name = ?";
        $stmt = $this->conn->prepare($checkSql);
        $stmt->bind_param("s", $data['setting_name']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return [
                'success' => false,
                'message' => 'Tên cấu hình đã tồn tại'
            ];
        }
        
        $sql = "INSERT INTO SystemSettings (setting_name, setting_value, last_updated, updated_by)
                VALUES (?, ?, NOW(), ?)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssi", 
            $data['setting_name'],
            $data['setting_value'],
            $managerId
        );
        
        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'Thêm cấu hình thành công',
                'setting_id' => $this->conn->insert_id
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Lỗi khi thêm cấu hình: ' . $stmt->error
            ];
        }
    }
    
    /**
     * Cập nhật cấu hình
     */
    public function updateSetting($settingId, $settingValue, $managerId) {
        $sql = "UPDATE SystemSettings 
                SET setting_value = ?, 
                    last_updated = NOW(), 
                    updated_by = ?
                WHERE setting_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sii", $settingValue, $managerId, $settingId);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                return [
                    'success' => true,
                    'message' => 'Cập nhật cấu hình thành công'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Không có thay đổi hoặc không tìm thấy cấu hình'
                ];
            }
        } else {
            return [
                'success' => false,
                'message' => 'Lỗi khi cập nhật cấu hình: ' . $stmt->error
            ];
        }
    }
    
    /**
     * Xóa cấu hình
     */
    public function deleteSetting($settingId) {
        $sql = "DELETE FROM SystemSettings WHERE setting_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $settingId);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                return [
                    'success' => true,
                    'message' => 'Xóa cấu hình thành công'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy cấu hình'
                ];
            }
        } else {
            return [
                'success' => false,
                'message' => 'Lỗi khi xóa cấu hình: ' . $stmt->error
            ];
        }
    }
}
?>
