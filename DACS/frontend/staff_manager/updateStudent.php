<?php
require_once "../../backend/controller/quanLySinhVienController.php";

$controller = new quanLySinhVienController();

$data = [
    "user_id"   => $_POST['user_id'],
    "full_name" => $_POST['full_name'],
    "gender"    => $_POST['gender'],
    "email"     => $_POST['email'],
    "phone"     => $_POST['phone'],
    "faculty"   => $_POST['faculty'],
    "major"     => $_POST['major'],
    "date_of_birth"     => $_POST['date_of_birth'],
    "address"   => $_POST['address'],
];

echo json_encode($controller->updateSV($data));