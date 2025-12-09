<?php
class Student {
    public $student_id;
    public $faculty;
    public $major;
    public $gender;
    public $date_of_birth;
    public $address;

    function __construct($id, $faculty, $major, $gender, $date_of_birth, $address) {
        $this->student_id = $id;
        $this->faculty = $faculty;
        $this->major = $major;
        $this->gender = $gender;
        $this->date_of_birth = $date_of_birth;
        $this->address = $address;
    }
}
?>
