<?php 
include_once __DIR__ . '/../database.php';
class quanLySinhVienModel{
    private $conn;
    
    public function __construct(){
        global $conn;
        $this->conn = $conn;
    }   

    //lấy danh sách sinh viên
    public function getAllStudent(){
         $sql = "
            SELECT 
                u.user_id,
                u.username,
                u.password,
                u.full_name,
                u.email,
                u.phone,
                s.student_id,
                s.faculty,
                s.major,
                s.gender,
                s.date_of_birth,
                s.address
            FROM Student s
            INNER JOIN Users u ON s.user_id = u.user_id
            ORDER BY u.user_id ASC
        ";
        $result = mysqli_query($this->conn, $sql);

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }

        return $data;
    }

    public function deleteStudent($user_id){
        $sql = "DELETE FROM Users WHERE user_id = $user_id";

        if (mysqli_query($this->conn, $sql)) {
            return ["status" => "success", "message" => "Deleted successfully"];
        } else {
            return ["status" => "error", "message" => mysqli_error($this->conn)];
        }
    }

    // lây 1 sinh viên
    public function getById($user_id){
        $sql = "
            SELECT u.user_id,
                u.username,
                u.full_name,
                u.email,
                u.phone,
                s.student_id,
                s.faculty,
                s.major,
                s.gender,
                s.date_of_birth,
                s.address
            FROM Student s
            INNER JOIN Users u ON s.user_id = u.user_id
            WHERE u.user_id = $user_id
        ";

        $result = mysqli_query($this->conn, $sql);
        return mysqli_fetch_assoc($result);
    }


    public function updateStudent($data){
        $user_id  = $data['user_id'];
        $full_name = $data['full_name'];
        $gender = $data['gender'];
        $email     = $data['email'];
        $phone     = $data['phone'];
        $faculty   = $data['faculty'];
        $major     = $data['major'];
        $date_of_birth     = $data['date_of_birth'];
        $address   = $data['address'];

        // Update Users table
        $sql1 = "
            UPDATE Users 
            SET full_name='$full_name', email='$email', phone='$phone'
            WHERE user_id = $user_id
        ";

        // Update Student table
        $sql2 = "
            UPDATE Student 
            SET faculty='$faculty', major='$major', address='$address', gender='$gender',date_of_birth='$date_of_birth'
            WHERE user_id = $user_id
        ";

        if(mysqli_query($this->conn, $sql1) && mysqli_query($this->conn, $sql2)){
            return ["status" => "success"];
        } else {
return ["status" => "error", "message" => mysqli_error($this->conn)];
        }
    }

    public function createStudent($data){
        $username      = $data['username'];
        $password_hash = password_hash($data['password'], PASSWORD_BCRYPT);
        $full_name     = $data['full_name'];
        $email         = $data['email'];
        $phone         = $data['phone'];
        $role          = "student";

        $student_id    = $data['student_id'];
        $faculty       = $data['faculty'];
        $major         = $data['major'];
        $gender        = $data['gender'];
        $dob           = $data['date_of_birth'];
        $address       = $data['address'];

        // 1️⃣ Insert Users → trigger sẽ tạo record Student
        $sql1 = "
            INSERT INTO Users (username, password, full_name, email, phone, role)
            VALUES ('$username', '$password_hash', '$full_name', '$email', '$phone', '$role')
        ";

        if(!mysqli_query($this->conn, $sql1)){
            return ["status" => "error", "message" => mysqli_error($this->conn)];
        }

        // 2️⃣ Lấy user_id vừa tạo
        $user_id = mysqli_insert_id($this->conn);

        // 3️⃣ Update lại Student do trigger tạo ra (đang rỗng)
        $sql2 = "
            UPDATE Student
            SET student_id   = '$student_id',
                faculty      = '$faculty',
                major        = '$major',
                gender       = '$gender',
                date_of_birth= '$dob',
                address      = '$address'
            WHERE user_id = $user_id
        ";

        if(mysqli_query($this->conn, $sql2)){
            return ["status" => "success"];
        } else {
            return ["status" => "error", "message" => mysqli_error($this->conn)];
        }
    }




}
?>