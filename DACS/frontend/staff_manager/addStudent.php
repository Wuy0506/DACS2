<?php
include_once "../../backend/controller/quanLySinhVienController.php";

$controller = new quanLySinhVienController();

$data = [
    "username"      => $_POST['username'],
    "password"      => $_POST['password'],
    "student_id"    => $_POST['student_id'],
    "full_name"     => $_POST['full_name'],
    "gender"        => $_POST['gender'],
    "date_of_birth" => $_POST['date_of_birth'],
    "faculty"       => $_POST['faculty'],
    "major"         => $_POST['major'],
    "address"       => $_POST['address'],
    "email"         => $_POST['email'],
    "phone"         => $_POST['phone']
];

echo json_encode($controller->createSV($data));