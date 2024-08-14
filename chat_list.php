<?php
session_start();
require 'db/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'supervisor') {
    die("You must be logged in as a supervisor to view this page.");
}

$supervisor_id = $_SESSION['user_id'];

// Fetch the list of students who have sent messages
$query = "SELECT DISTINCT students.id, students.name FROM students JOIN messages ON students.id = messages.student_id WHERE messages.supervisor_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $supervisor_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat List</title>
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
            margin: 0 auto;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #007bff;
            margin-bottom: 20px;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 10px;
            padding: 15px;
            transition: background-color 0.3s, box-shadow 0.3s;
        }

        li a {
            text-decoration: none;
            color: #333;
            font-size: 18px;
        }

        li:hover {
            background-color: #e9ecef;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Students who sent messages</h1>
        <ul>
            <?php while ($row = $result->fetch_assoc()): ?>
                <li><a href="supervisor_chat.php?student_id=<?= htmlspecialchars($row['id']) ?>"><?= htmlspecialchars($row['name']) ?></a></li>
            <?php endwhile; ?>
        </ul>
    </div>
</body>
</html>
