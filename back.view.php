<?php include 'includes/header.php'; ?>
<?php include 'db/database.php'; ?>

<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'coordinator') {
    header('Location: login_form.php');
    exit();
}

// Get student ID from query parameters
$student_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch student details
$student_result = $conn->query("SELECT * FROM students WHERE id = $student_id");
$student = $student_result->fetch_assoc();

if (!$student) {
    die("Student not found.");
}

// Fetch student progress comments
$comments_result = $conn->query("SELECT * FROM supervisor_comments WHERE student_id = $student_id ORDER BY date DESC");
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
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Student Progress</h1>
    <h2>Student Details</h2>
    <?php if ($student): ?>
        <p><strong>Reg No:</strong> <?php echo htmlspecialchars($student['reg_no']); ?></p>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($student['name']); ?></p>
        <p><strong>Program:</strong> <?php echo htmlspecialchars($student['program']); ?></p>
        <p><strong>Status:</strong> <?php echo htmlspecialchars($student['status']); ?></p>
    <?php else: ?>
        <p>Student not found.</p>
    <?php endif; ?>

    <h2>Progress Comments</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Comment</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($comments_result->num_rows > 0): ?>
                <?php while ($comment = $comments_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($comment['date']); ?></td>
                        <td><?php echo htmlspecialchars($comment['comment']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="2">No comments found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
<?php include 'includes/footer.php'; ?>
