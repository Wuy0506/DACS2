<?php
include_once '../../backend/controller/quanLySinhVienController.php';

if (!isset($_GET['id'])) {
    echo json_encode(["status" => "error", "message" => "No ID provided"]);
    exit;
}

$id = $_GET['id'];

$controller = new quanLySinhVienController();
$result = $controller->deleteSV($id);

echo json_encode($result);
?>