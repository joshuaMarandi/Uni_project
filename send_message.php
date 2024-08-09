<?php
session_start();
require 'db/database.php';

if (!isset($_SESSION['student_id'])) {
    header('Location: student_login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_SESSION['student_id'];
    $message = $_POST['message'];
    $supervisor_id = $_POST['supervisor_id']; // Fetch this from student details or session

    $stmt = $conn->prepare("INSERT INTO messages (student_id, supervisor_id, message) VALUES (?, ?, ?)");
    $stmt->bind_param('iis', $student_id, $supervisor_id, $message);
    if ($stmt->execute()) {
        header('Location: student_panel.php');
    } else {
        echo 'Failed to send message';
    }
}
