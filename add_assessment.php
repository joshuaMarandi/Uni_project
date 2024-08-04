<?php
session_start();
include 'db/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'coordinator') {
    header('Location: login_form.php');
    exit();
}

// Retrieve form data
$student_id = $_POST['student_id']; // Ensure form method is POST
$assessment_status = $_POST['assessment_status'];
$comments = $_POST['comments'];
$coordinator_id = $_SESSION['user_id']; // Logged-in coordinator ID

// Prepare the SQL statement
$sql = "INSERT INTO assessment (student_id, coordinator_id, assessment_status, comments) VALUES (?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}

// Bind parameters
$stmt->bind_param("iiss", $student_id, $coordinator_id, $assessment_status, $comments);

if (!$stmt->execute()) {
    die('Execute failed: ' . htmlspecialchars($stmt->error));
}

echo "Data inserted successfully";

$stmt->close();
$conn->close();
?>
