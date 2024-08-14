<?php
session_start();
require 'db/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $message = $conn->real_escape_string($_POST['message']);
    $sender_type = isset($_SESSION['student_id']) ? 'student' : 'supervisor';

    // Fetch supervisor_id for the student
    $stmt = $conn->prepare("SELECT supervisor_id FROM students WHERE id = ?");
    $stmt->bind_param('i', $student_id);
    $stmt->execute();
    $stmt->bind_result($supervisor_id);
    $stmt->fetch();
    $stmt->close();

    if ($supervisor_id) {
        // Insert the message into the messages table
        $query = "INSERT INTO messages (student_id, supervisor_id, message, sender_type) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('iiss', $student_id, $supervisor_id, $message, $sender_type);
        $stmt->execute();
        $stmt->close();

        header("Location: {$_SERVER['HTTP_REFERER']}");
        exit();
    } else {
        die("Supervisor ID is not assigned for this student.");
    }
}
?>
