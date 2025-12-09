<?php
// Lightweight endpoint to return the student's approved room registrations as JSON
require_once __DIR__ . '/roomOfStudentController.php';

$ctrl = new RoomOfStudentController();
$ctrl->handleRequest();