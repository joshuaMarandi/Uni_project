<?php include 'db/database.php'; ?>

<?php    
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'supervisor') {
    header('Location: login_form.php');
    exit();
}

// Initialize variables
$user_id = $_SESSION['user_id'];
$result = null;

// Fetch students supervised by the current user (supervisor)
$stmt = $conn->prepare("SELECT s.* FROM students s WHERE s.supervisor_id = ?");
if ($stmt) {
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
} else {
    die("Failed to prepare SQL statement: " . $conn->error);
}

// Handle approval action
if (isset($_GET['approve_id'])) {
    $approve_id = intval($_GET['approve_id']);
    
    // Update the student's status to 'approved'
    $update_stmt = $conn->prepare("UPDATE students SET status = 'approved' WHERE id = ? AND supervisor_id = ?");
    if ($update_stmt) {
        $update_stmt->bind_param('ii', $approve_id, $user_id);
        if ($update_stmt->execute()) {
            echo '<p>Student approved successfully.</p>';
        } else {
            echo '<p>Failed to approve student.</p>';
        }
        $update_stmt->close();
    } else {
        die("Failed to prepare SQL statement: " . $conn->error);
    }
    // Refresh the page to reflect changes
    header('Location: supervisor_dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervisor Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 5px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        td.pending {
            background-color: #F4C542; /* yellow */
            color: black;
        }

        td.approved {
            background-color: #4CAF50; /* green */
            color: white;
        }

        td.verified {
            background-color: #2196F3; /* Blue */
            color: white;
        }

        td.pending::before {
            content: "\f017"; /* Clock icon */
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            margin-right: 8px;
        }

        td.approved::before {
            content: "\f164"; /* Thumbs up icon */
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            margin-right: 8px;
        }

        td.verified::before {
            content: "\f058"; /* Check circle icon */
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            margin-right: 8px;
        }

        button {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            color: #fff;
            background-color: #007bff;
            cursor: pointer;
        }

        button:disabled {
            background-color: #6c757d;
            cursor: not-allowed;
        }

        .btn-view-progress {
            background-color: #28a745;
        }

        .btn-update-progress {
            background-color: #ffc107;
        }

        .btn-approve {
            background-color: #007bff;
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
    <h1>Supervisor Dashboard</h1>
<a href="chat_list.php" class="btn green">Chat here</a>
    <h2>Student Assessment</h2>
    <table>
        <thead>
            <tr>
                <th>Reg No</th>
                <th>Name</th>
                <th>Program</th>
                <th>Status</th>
                <th>View Progress</th>
                <th>Update Progress</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td data-label="Reg No"><?php echo htmlspecialchars($row['reg_no']); ?></td>
                        <td data-label="Name"><?php echo htmlspecialchars($row['name']); ?></td>
                        <td data-label="Program" ><?php echo htmlspecialchars($row['program']); ?></td>
                        <td data-label="Status" class="<?php echo strtolower($row['status']); ?>" >
                            <?php echo htmlspecialchars($row['status']); ?>
                        </td>
                        <td data-label="View Progress">
                            <a href="view_progress.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="btn btn-view-progress">View Progress</a>
                        </td>
                        <td data-label="Update Progress">
                            <a href="update_progress.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="btn btn-update-progress">Update Progress</a>
                        </td>
                        <td data-label="Action" >
                            <?php if ($row['status'] === 'pending'): ?>
                                <a href="?approve_id=<?php echo htmlspecialchars($row['id']); ?>" class="btn btn-approve">Approve</a>
                            <?php else: ?>
                                <span>N/A</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">No students found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
<?php include 'includes/footer.php'; ?>
