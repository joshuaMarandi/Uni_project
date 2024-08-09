<?php
session_start();
require 'db/database.php';

if (!isset($_SESSION['student_id'])) {
    // header('Location: student_login.php');
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch student details
$stmt = $conn->prepare("
    SELECT s.*, su.name AS supervisor_name, c.name AS coordinator_name
    FROM students s
    JOIN users su ON s.supervisor_id = su.id
    JOIN users c ON s.coordinator_id = c.id
    WHERE s.id = ?
");
$stmt->bind_param('i', $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

// Fetch chat messages
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
        }
        #chat-box {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
            max-height: 300px;
            overflow-y: scroll;
        }
        .message {
            margin-bottom: 10px;
        }
        .message strong {
            color: #007bff;
        }
        textarea {
            width: 100%;
            height: 100px;
            margin-bottom: 10px;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($student['name']); ?></h1>

    <h2>Details</h2>
    <p>Program: <?php echo htmlspecialchars($student['program']); ?></p>
    <p>Project Title: <?php echo htmlspecialchars($student['project_title']); ?></p>
    <p>Academic Year: <?php echo htmlspecialchars($student['academic_year']); ?></p>
    <p>Status: <?php echo htmlspecialchars($student['status']); ?></p>
    <p>Supervisor: <?php echo htmlspecialchars($student['supervisor_name']); ?></p>
    <p>Coordinator: <?php echo htmlspecialchars($student['coordinator_name']); ?></p>

    <h2>Chat with Supervisor</h2>
    <div id="chat-box">
        <?php while ($msg = $messages->fetch_assoc()): ?>
            <div class="message">
                <strong><?php echo htmlspecialchars($msg['student_name'] === $student['name'] ? 'You' : 'Supervisor'); ?>:</strong>
                <p><?php echo htmlspecialchars($msg['message']); ?></p>
                <small><?php echo htmlspecialchars($msg['created_at']); ?></small>
            </div>
        <?php endwhile; ?>
    </div>

    <form method="POST" action="send_message.php">
        <textarea name="message" required></textarea>
        <input type="hidden" name="supervisor_id" value="<?php echo htmlspecialchars($student['supervisor_id']); ?>">
        <button type="submit">Send</button>
    </form>
</body>
</html>
