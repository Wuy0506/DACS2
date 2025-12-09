<?php
/**
 * Statistics Model
 * Xử lý các chức năng thống kê dành cho Manager
 */

require_once __DIR__ . '/../database.php';

class StatisticsModel {
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
        
        if (!$this->conn) {
            die("Database connection failed");
        }
    }
    
    /**
     * Lấy tổng quan thống kê hệ thống
     */
    public function getOverallStatistics() {
        // Thống kê sinh viên
        $sqlStudents = "SELECT 
                        COUNT(*) as total_students,
                        COUNT(CASE WHEN s.student_id IS NOT NULL THEN 1 END) as registered_students
                        FROM Users u
                        LEFT JOIN Student s ON u.user_id = s.user_id
                        WHERE u.role = 'student'";
        
        $result = $this->conn->query($sqlStudents);
        $students = $result->fetch_assoc();
        
        // Thống kê phòng
        $sqlRooms = "SELECT 
                     COUNT(*) as total_rooms,
                     SUM(CASE WHEN status = 'Trống' THEN 1 ELSE 0 END) as empty_rooms,
                     SUM(CASE WHEN status = 'Đầy' THEN 1 ELSE 0 END) as full_rooms,
                     SUM(CASE WHEN status = 'Bảo trì' THEN 1 ELSE 0 END) as maintenance_rooms,
                     SUM(capacity) as total_beds,
                     SUM(available_beds) as available_beds
                     FROM Room";
        
        $result = $this->conn->query($sqlRooms);
        $rooms = $result->fetch_assoc();
        
        // Thống kê hợp đồng
        $sqlContracts = "SELECT 
                         COUNT(*) as total_contracts,
                         SUM(CASE WHEN status = 'Hiệu lực' THEN 1 ELSE 0 END) as active_contracts,
                         SUM(CASE WHEN status = 'Hết hạn' THEN 1 ELSE 0 END) as expired_contracts
                         FROM Contract";
        
        $result = $this->conn->query($sqlContracts);
        $contracts = $result->fetch_assoc();
        
        // Thống kê đăng ký phòng
        $sqlRegistrations = "SELECT 
                              COUNT(*) as total_registrations,
                              SUM(CASE WHEN status = 'Chờ duyệt' THEN 1 ELSE 0 END) as pending_registrations,
                              SUM(CASE WHEN status = 'Đã duyệt' THEN 1 ELSE 0 END) as approved_registrations
                              FROM RoomRegistration";
        
        $result = $this->conn->query($sqlRegistrations);
        $registrations = $result->fetch_assoc();
        
        return [
            'success' => true,
            'statistics' => [
                'students' => $students,
                'rooms' => $rooms,
                'contracts' => $contracts,
                'registrations' => $registrations
            ]
        ];
    }
    
    /**
     * Lấy thống kê doanh thu theo thời gian
     */
    public function getRevenueStatistics($startDate = null, $endDate = null) {
        $sql = "SELECT 
                payment_type,
                COUNT(*) as total_payments,
                SUM(amount) as total_amount
                FROM Payment";
        
        $conditions = [];
        $params = [];
        $types = "";
        
        if ($startDate) {
            $conditions[] = "payment_date >= ?";
            $params[] = $startDate;
            $types .= "s";
        }
        
        if ($endDate) {
            $conditions[] = "payment_date <= ?";
            $params[] = $endDate;
            $types .= "s";
        }
        
        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $sql .= " GROUP BY payment_type";
        
        $stmt = $this->conn->prepare($sql);
        
        if (count($params) > 0) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $revenue = [];
        $totalRevenue = 0;
        
        while ($row = $result->fetch_assoc()) {
            $revenue[] = $row;
            $totalRevenue += $row['total_amount'];
        }
        
        return [
            'success' => true,
            'revenue' => $revenue,
            'total_revenue' => $totalRevenue
        ];
    }
    
    /**
     * Lấy thống kê chi phí sửa chữa
     */
    public function getRepairCostStatistics($startDate = null, $endDate = null) {
        $sql = "SELECT 
                status,
                COUNT(*) as total_repairs,
                SUM(estimated_cost) as total_estimated_cost,
                SUM(actual_cost) as total_actual_cost
                FROM RepairRequest";
        
        $conditions = [];
        $params = [];
        $types = "";
        
        if ($startDate) {
            $conditions[] = "report_date >= ?";
            $params[] = $startDate;
            $types .= "s";
        }
        
        if ($endDate) {
            $conditions[] = "report_date <= ?";
            $params[] = $endDate;
            $types .= "s";
        }
        
        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $sql .= " GROUP BY status";
        
        $stmt = $this->conn->prepare($sql);
        
        if (count($params) > 0) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $repairs = [];
        $totalEstimated = 0;
        $totalActual = 0;
        
        while ($row = $result->fetch_assoc()) {
            $repairs[] = $row;
            $totalEstimated += $row['total_estimated_cost'] ?? 0;
            $totalActual += $row['total_actual_cost'] ?? 0;
        }
        
        return [
            'success' => true,
            'repairs' => $repairs,
            'total_estimated_cost' => $totalEstimated,
            'total_actual_cost' => $totalActual
        ];
    }
    
    /**
     * Lấy thống kê theo tháng (doanh thu và chi phí)
     */
    public function getMonthlyStatistics($year) {
        $sql = "SELECT 
                MONTH(payment_date) as month,
                payment_type,
                SUM(amount) as total
                FROM Payment
                WHERE YEAR(payment_date) = ?
                GROUP BY MONTH(payment_date), payment_type
                ORDER BY MONTH(payment_date)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $year);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $monthly = [];
        while ($row = $result->fetch_assoc()) {
            $month = $row['month'];
            if (!isset($monthly[$month])) {
                $monthly[$month] = [
                    'month' => $month,
                    'revenue' => 0,
                    'types' => []
                ];
            }
            $monthly[$month]['revenue'] += $row['total'];
            $monthly[$month]['types'][$row['payment_type']] = $row['total'];
        }
        
        // Lấy chi phí sửa chữa theo tháng
        $sqlRepair = "SELECT 
                      MONTH(report_date) as month,
                      SUM(actual_cost) as total_cost
                      FROM RepairRequest
                      WHERE YEAR(report_date) = ? AND status = 'Hoàn thành'
                      GROUP BY MONTH(report_date)";
        
        $stmt = $this->conn->prepare($sqlRepair);
        $stmt->bind_param("i", $year);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $month = $row['month'];
            if (!isset($monthly[$month])) {
                $monthly[$month] = [
                    'month' => $month,
                    'revenue' => 0,
                    'types' => []
                ];
            }
            $monthly[$month]['repair_cost'] = $row['total_cost'];
        }
        
        return [
            'success' => true,
            'monthly_statistics' => array_values($monthly)
        ];
    }
    
    /**
     * Lấy thống kê phòng theo tòa nhà
     */
    public function getRoomStatisticsByBuilding() {
        $sql = "SELECT 
                building,
                COUNT(*) as total_rooms,
                SUM(capacity) as total_capacity,
                SUM(available_beds) as available_beds,
                SUM(CASE WHEN status = 'Trống' THEN 1 ELSE 0 END) as empty_rooms,
                SUM(CASE WHEN status = 'Đầy' THEN 1 ELSE 0 END) as full_rooms,
                SUM(CASE WHEN status = 'Bảo trì' THEN 1 ELSE 0 END) as maintenance_rooms
                FROM Room
                GROUP BY building
                ORDER BY building";
        
        $result = $this->conn->query($sql);
        
        $buildings = [];
        while ($row = $result->fetch_assoc()) {
            $buildings[] = $row;
        }
        
        return [
            'success' => true,
            'buildings' => $buildings
        ];
    }
}
?>
