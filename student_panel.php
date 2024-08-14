<?php
session_start();
require 'db/database.php';

if (!isset($_SESSION['student_id'])) {
    // header('Location: student_login.php');
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch student details and supervisor information
$stmt = $conn->prepare("
    SELECT s.*, su.name AS supervisor_name, su.phone AS supervisor_phone
    FROM students s
    JOIN users su ON s.supervisor_id = su.id
    WHERE s.id = ?
");
$stmt->bind_param('i', $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

// Fetch chat messages (optional for this page)
$messages_stmt = $conn->prepare("
    SELECT m.*, u1.name AS student_name, u2.name AS supervisor_name
    FROM messages m
    JOIN users u1 ON m.student_id = u1.id
    JOIN users u2 ON m.supervisor_id = u2.id
    WHERE m.student_id = ? OR m.supervisor_id = ?
    ORDER BY m.created_at
");
$messages_stmt->bind_param('ii', $student_id, $student['supervisor_id']);
$messages_stmt->execute();
$messages = $messages_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #007bff;
            margin-bottom: 20px;
        }

        h2 {
            color: #333;
            margin-top: 0;
        }

        p {
            font-size: 16px;
            color: #555;
            margin: 10px 0;
        }

        .button-container {
            margin-bottom: 20px;
        }

        .btn {
            display: inline-block;
            text-decoration: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            color: white;
            background-color: #007bff;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .grey {
            background-color: #6c757d;
        }

        .grey:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($student['name']); ?></h1>
        <div class="button-container">
            <a href="student_chat.php" class="btn grey">Chat Here</a>
        </div>
        <h2>Details</h2>
        <p><strong>Program:</strong> <?php echo htmlspecialchars($student['program']); ?></p>
        <p><strong>Project Title:</strong> <?php echo htmlspecialchars($student['project_title']); ?></p>
        <p><strong>Academic Year:</strong> <?php echo htmlspecialchars($student['academic_year']); ?></p>
        <p><strong>Status:</strong> <?php echo htmlspecialchars($student['status']); ?></p>
        <p><strong>Supervisor:</strong> <?php echo htmlspecialchars($student['supervisor_name']); ?></p>
        <p><strong>Supervisor Phone:</strong> <?php echo htmlspecialchars($student['supervisor_phone']); ?></p>
    </div>
</body>
</html>
