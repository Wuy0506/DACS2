<?php
include_once __DIR__ . '/../database.php';

class RoomOfStudentModel {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }


    // Lấy thông tin phòng (một phòng đã được duyệt gần nhất) cùng thông tin bạn cùng phòng và thống kê giường
    public function getRoomAndRoommates($userId) {
        $sql = "
            SELECT 
                u.user_id,
                u.full_name,
                u.email,
                u.phone,
                s.student_id AS mssv,
                s.faculty,
                s.major,
                s.gender,
                s.date_of_birth,

                -- Thông tin đăng ký phòng
                rr.room_id,
                rr.registration_id,
                rr.start_date,
                rr.end_date,
                rr.status AS registration_status,

                -- Thông tin phòng
                r.room_name,
                r.building,
                r.floor,
                r.capacity,
                r.price_per_month,
                r.gender_restriction,

                -- Giường của bản thân
                b.bed_id AS my_bed_id,
                b.bed_number AS my_bed_number,
                b.status AS my_bed_status,

                -- Bạn cùng phòng
                u2.user_id AS roommate_id,
                u2.full_name AS roommate_name,
                u2.email AS roommate_email,
                u2.phone AS roommate_phone,
                s2.student_id AS roommate_student_id,
                s2.faculty AS roommate_faculty,
                s2.major AS roommate_major,
                s2.gender AS roommate_gender,
                s2.date_of_birth AS roommate_dob,
                b2.bed_number AS roommate_bed_number

            FROM Users u
            JOIN Student s ON u.user_id = s.user_id

            JOIN RoomRegistration rr 
                ON rr.student_id = u.user_id
                AND rr.status = 'Đã duyệt'

            JOIN Room r ON rr.room_id = r.room_id

            LEFT JOIN Bed b 
                ON b.bed_id = rr.bed_id

            LEFT JOIN RoomRegistration rr2 
                ON rr2.room_id = r.room_id
                AND rr2.status = 'Đã duyệt'
                AND rr2.student_id != u.user_id

            LEFT JOIN Users u2 ON u2.user_id = rr2.student_id
            LEFT JOIN Student s2 ON s2.user_id = rr2.student_id

            LEFT JOIN Bed b2 
                ON b2.bed_id = rr2.bed_id

            WHERE u.user_id = ?
        ";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return null;

        $stmt->bind_param("i", $userId);
        $stmt->execute();

        $result = $stmt->get_result();
        $rows = $result->fetch_all(MYSQLI_ASSOC);

        if (count($rows) === 0) return null;

        // --- THÔNG TIN BẢN THÂN ---
        $me = [
"user_id" => $rows[0]["user_id"],
            "full_name" => $rows[0]["full_name"],
            "email" => $rows[0]["email"],
            "phone" => $rows[0]["phone"],
            "student_id" => $rows[0]["mssv"],
            "faculty" => $rows[0]["faculty"],
            "major" => $rows[0]["major"],
            "gender" => $rows[0]["gender"],
            "date_of_birth" => $rows[0]["date_of_birth"],
            "bed_number" => $rows[0]["my_bed_number"]
        ];

        // --- THÔNG TIN PHÒNG ---
        $room = [
            "room_name" => $rows[0]["room_name"],
            "room_id" => $rows[0]["room_id"],
            "building" => $rows[0]["building"],
            "floor" => $rows[0]["floor"],
            "capacity" => $rows[0]["capacity"],
            "price_per_month" => $rows[0]["price_per_month"],
            "gender_restriction" => $rows[0]["gender_restriction"]
        ];

        // --- DANH SÁCH BẠN CÙNG PHÒNG ---
        $roommates = [];

        foreach ($rows as $r) {
            if ($r["roommate_id"]) {
                $roommates[] = [
                    "user_id" => $r["roommate_id"],
                    "full_name" => $r["roommate_name"],
                    "email" => $r["roommate_email"],
                    "phone" => $r["roommate_phone"],
                    "student_id" => $r["roommate_student_id"],
                    "faculty" => $r["roommate_faculty"],
                    "major" => $r["roommate_major"],
                    "gender" => $r["roommate_gender"],
                    "date_of_birth" => $r["roommate_dob"],
                    "bed_number" => $r["roommate_bed_number"]
                ];
            }
        }

        return [
            "me" => $me,
            "room" => $room,
            "roommates" => $roommates
        ];
    }


    

}


?>