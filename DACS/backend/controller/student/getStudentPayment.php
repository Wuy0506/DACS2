<?php
/**
 * getStudentPayment.php
 * API endpoint xử lý thanh toán cho sinh viên
 */
require_once __DIR__ . '/StudentPaymentController.php';

$controller = new StudentPaymentController();
$controller->handleRequest();
?>
