<?php //include 'includes/header.php'; ?>
<?php include 'db/database.php'; ?>

<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'supervisor') {
    header('Location: login_form.php');
    exit();
}

// Initialize variables
$student_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$comments = [];

// Fetch progress comments for the student
$stmt = $conn->prepare("SELECT * FROM supervisor_comments WHERE student_id = ?");
if ($stmt) {
    $stmt->bind_param('i', $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $comments[] = $row;
    }
    $stmt->close();
} else {
    die("Failed to prepare SQL statement: " . $conn->error);
}

// Fetch student details for the header
$student_stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
if ($student_stmt) {
    $student_stmt->bind_param('i', $student_id);
    $student_stmt->execute();
    $student = $student_stmt->get_result()->fetch_assoc();
    $student_stmt->close();
} else {
    die("Failed to prepare SQL statement: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Progress</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .container {
            padding: 20px;
            max-width: 1200px;
            margin: auto;
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            color: #333;
        }

        td {
            color: #555;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tbody tr:nth-child(odd) {
            background-color: #fff;
        }

        tbody tr:hover {
            background-color: #eaeaea;
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            th, td {
                padding: 8px;
                font-size: 14px;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 5px;
            }

            th, td {
                padding: 6px;
                font-size: 12px;
                display: block;
                width: 100%;
                box-sizing: border-box;
            }

            th {
                background-color: #f2f2f2;
                display: none;
            }

            td {
                display: flex;
                justify-content: space-between;
                border-top: 1px solid #ddd;
            }

            td::before {
                content: attr(data-label);
                flex: 1;
                font-weight: bold;
                color: #333;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Progress Details for <?php echo htmlspecialchars($student['name']); ?></h1>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Comment</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($comments)): ?>
                <?php foreach ($comments as $comment): ?>
                    <tr>
                        <td data-label="Date"><?php echo htmlspecialchars($comment['date']); ?></td>
                        <td data-label="Comment"><?php echo htmlspecialchars($comment['comment']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="2">No progress details available.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
<?php include 'includes/footer.php'; ?>
