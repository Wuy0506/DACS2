<?php
/**
 * Entry point cho chức năng tìm kiếm phòng
 * URL: /backend/search.php?action=search&checkIn=...&checkOut=...&people=...
 */

// Include controller
include_once __DIR__ . '/controller/SearchRoomController.php';

// Tạo instance và chạy
$controller = new SearchRoomController();
$controller->run();
