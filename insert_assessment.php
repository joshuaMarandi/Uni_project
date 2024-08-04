<?php
// Start session and include database connection
session_start();
include 'db/database.php';

// Check if user is logged in and has correct role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'coordinator') {
    header('Location: login_form.php');
    exit();
}

// Sample data (replace with actual data from form or other sources)
$student_id = 1; // Example student ID
$coordinator_id = $_SESSION['user_id']; // Logged-in coordinator ID
$assessment_status = 'Completed';
$comments = 'Project is on track';

// Prepare the SQL statement
$sql = "INSERT INTO assessments (student_id, coordinator_id, assessment_status, comments) VALUES (?, ?, ?, ?)";

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




