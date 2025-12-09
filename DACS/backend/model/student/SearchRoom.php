<?php
include_once __DIR__ . '/../database.php';

/**
 * Model xử lý tìm kiếm phòng
 */
class SearchRoomModel {
    private $conn;
    
    function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    /**
     * Tìm kiếm phòng theo số người, giới tính, tình trạng
     */
    public function searchRooms($people = 1, $gender = '', $status = '') {
        // Build WHERE conditions
        $conditions = ["r.available_beds > 0", "r.capacity >= '$people'"];

        // Filter: chỉ hiển thị phòng có giường trống
    $conditions[] = "(
        SELECT COUNT(*) 
        FROM Bed b 
        WHERE b.room_id = r.room_id AND b.status = 'Trống'
    ) > 0";
        
        // Filter theo giới tính
        if (!empty($gender)) {
            $conditions[] = "(r.gender_restriction = '$gender' OR r.gender_restriction = 'Không giới hạn')";
        }
        
        // Filter theo tình trạng
        if (!empty($status)) {
            $conditions[] = "r.status = '$status'";
        } else {
            // Mặc định chỉ lấy phòng trống
            $conditions[] = "r.status = 'Trống'";
        }
        
        $whereClause = implode(' AND ', $conditions);
        
        // Query lấy danh sách phòng
        $sql = "SELECT 
                    r.room_id,
                    r.room_name,
                    r.building,
                    r.floor,
                    r.capacity,
                    r.available_beds,
                    r.gender_restriction,
                    r.price_per_month,
                    r.image,
                    r.status,
                (SELECT COUNT(*) FROM Bed b 
                 WHERE b.room_id = r.room_id AND b.status = 'Trống') AS giuongTrong
                FROM Room r
                WHERE $whereClause
                ORDER BY r.available_beds DESC, r.price_per_month ASC";
        
        $result = mysqli_query($this->conn, $sql);
        
        if (!$result) {
            return [
                'success' => false,
                'message' => 'Lỗi khi tìm kiếm phòng: ' . mysqli_error($this->conn),
                'data' => []
            ];
        }
        
        $rooms = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rooms[] = [
                'id' => $row['room_id'],
                'name' => $row['room_name'],
                'building' => $row['building'],
                'floor' => $row['floor'],
                'capacity' => $row['capacity'],
                'available' => $row['available_beds'],
                'gender_restriction' => $row['gender_restriction'],
                'price' => $row['price_per_month'],
                'status' => $row['status'],
                'image' => $row['image'],
                // 'slug' => 'room-' . $row['room_id'],
                'giuongTrong' => $row['giuongTrong'].'/'.$row['capacity']
            ];
        }
        
        return [
            'success' => true,
            'data' => $rooms,
'count' => count($rooms)
        ];
    }
    
    /**
     * Lấy thông tin chi tiết một phòng theo ID
     */
    public function getRoomById($roomId) {
        $sql = "SELECT 
                    r.*
                FROM Room r
                WHERE r.room_id = '$roomId'
                LIMIT 1";
        
        $result = mysqli_query($this->conn, $sql);
        
        if (!$result) {
            return [
                'success' => false,
                'message' => 'Lỗi: ' . mysqli_error($this->conn)
            ];
        }
        
        if (mysqli_num_rows($result) > 0) {
            $room = mysqli_fetch_assoc($result);
            return [
                'success' => true,
                'data' => $room
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Không tìm thấy phòng'
        ];
    }
    
    /**
     * Lấy danh sách tất cả các khu nhà (building)
     */
    public function getAllBuildings() {
        $sql = "SELECT DISTINCT building FROM Room ORDER BY building";
        $result = mysqli_query($this->conn, $sql);
        
        if (!$result) {
            return [
                'success' => false,
                'message' => 'Lỗi: ' . mysqli_error($this->conn)
            ];
        }
        
        $buildings = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $buildings[] = $row['building'];
        }
        
        return [
            'success' => true,
            'data' => $buildings
        ];
    }
    
    /**
     * Lấy danh sách phòng theo khu nhà
     */
    public function getRoomsByBuilding($building) {
        // Thêm subquery đếm giuongTrong để hiển thị đúng trên thẻ
        $sql = "SELECT r.*
                
                FROM Room r 
                WHERE building = '$building' 
                AND r.status = 'Trống' 
                ORDER BY r.floor ASC, r.room_name ASC";
                
        $result = mysqli_query($this->conn, $sql);
        
        if (!$result) {
            return [
                'success' => false,
                'message' => 'Lỗi: ' . mysqli_error($this->conn)
            ];
        }
        
        $rooms = [];
        while ($row = mysqli_fetch_assoc($result)) {
            // Format lại dữ liệu giống như hàm searchRooms
            $row['available_beds'] = $row['available_beds'] . '/' . $row['capacity'];
            $rooms[] = $row;
        }
        
        return [
            'success' => true,
            'data' => $rooms
        ];
    }
    
    /**
     * Lấy danh sách phòng theo giới hạn giới tính
     */
    public function getRoomsByGender($gender) {
        $sql = "SELECT * FROM Room 
                WHERE (gender_restriction = '$gender' OR gender_restriction = 'Không giới hạn')
                AND status = 'Trống' 
                AND available_beds > 0
                ORDER BY building, floor";
        
        $result = mysqli_query($this->conn, $sql);
        
        if (!$result) {
            return [
                'success' => false,
                'message' => 'Lỗi: ' . mysqli_error($this->conn)
            ];
        }
        
        $rooms = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rooms[] = $row;
}
        
        return [
            'success' => true,
            'data' => $rooms
        ];
    }
}
