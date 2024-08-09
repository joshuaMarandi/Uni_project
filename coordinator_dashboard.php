<?php include 'db/database.php'; ?>

<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'coordinator') {
    header('Location: login_form.php');
    exit();
}

$coordinator_id = $_SESSION['user_id'];

// Fetch all supervisors added by the coordinator and their students
$query = "
    SELECT u.username AS supervisor_name, s.id AS student_id, s.reg_no, s.name AS student_name, s.program, s.status
    FROM students s
    INNER JOIN users u ON s.supervisor_id = u.id
    WHERE u.added_by_coordinator_id = ?
    ORDER BY u.username, s.reg_no
";

$stmt = $conn->prepare($query);
if ($stmt) {
    $stmt->bind_param('i', $coordinator_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Check if query returns any rows
    if ($result->num_rows === 0) {
        echo '<p>No supervisors found for your account.</p>';
    } else {
        $supervisors = [];
        while ($row = $result->fetch_assoc()) {
            $supervisors[$row['supervisor_name']][] = $row;
        }
    }
    
    $stmt->close();
} else {
    die("Failed to prepare SQL statement: " . $conn->error);
}

// Handle verification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_id'])) {
    $verify_id = intval($_POST['verify_id']);

    // Update the student's status to 'verified'
    $update_stmt = $conn->prepare("UPDATE students SET status = 'verified' WHERE id = ?");
    if ($update_stmt) {
        $update_stmt->bind_param('i', $verify_id);
        if ($update_stmt->execute()) {
            echo '<p>Student verified successfully.</p>';
        } else {
            echo '<p>Failed to verify student.</p>';
        }
        $update_stmt->close();
    } else {
        die("Failed to prepare SQL statement: " . $conn->error);
    }
    
    // Refresh the page to reflect changes
    header('Location: coordinator_dashboard.php');
    exit();
}

// Initialize counts
$pendingCount = 0;
$approvedCount = 0;
$verifiedCount = 0;

// Fetch counts of students by status
$sql = "SELECT status, COUNT(*) as count FROM students GROUP BY status";
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        switch ($row['status']) {
            case 'pending':
                $pendingCount = $row['count'];
                break;
            case 'approved':
                $approvedCount = $row['count'];
                break;
            case 'verified':
                $verifiedCount = $row['count'];
                break;
        }
    }
}

$conn->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Coordinator Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        .navbar {
            background-color: #333;
            color: #fff;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar a {
            color: #fff;
            text-decoration: none;
            margin-right: 15px;
        }

        .navbar .popup-menu {
            position: relative;
            display: inline-block;
        }

        .navbar .popup-menu .popup-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: #fff;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }

        .navbar .popup-menu:hover .popup-content {
            display: block;
        }

        .navbar .popup-menu a {
            color: #333;
            padding: 10px;
            text-decoration: none;
            display: block;
            width: 100%;
        }

        .navbar .popup-menu a:hover {
            background-color: #f1f1f1;
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
            text-align: center;
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

        .btn.view-progress {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            color: #fff;
            background-color: #6c757d; /* Grey background */
            text-decoration: none;
            cursor: pointer;
            display: inline-block;
            text-align: center;
        }

        .btn.view-progress:hover {
            background-color: #5a6268; /* Darker grey on hover */
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            th, td {
                padding: 8px;
                font-size: 14px;
            }

            button {
                width: 100%;
                padding: 12px;
                font-size: 16px;
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

            button {
                width: 100%;
                padding: 15px;
                font-size: 18px;
            }
        }
        .chart-container {
        position: relative;
        margin: auto;
        height: 400px;
        width: 80%;
    }
    </style>
</head>
<body>
<div class="navbar">
    <a href="index.php">Home</a>
    <a href="coordinator_dashboard.php">Supervisors</a>
    <a href="report.php">Report</a>
    <div class="popup-menu">
        <span>Menu</span>
        <div class="popup-content">
            <a href="add_student.php">Add Student</a>
            <a href="add_supervisor.php">Add Supervisor</a>
            <a href="Register.php">Register here</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</div>

<div class="container">
    <h1>Coordinator Dashboard</h1>
    
    <!-- Existing dashboard content -->
<?php
    if (!empty($supervisors)) {
        foreach ($supervisors as $supervisor_name => $students): ?>
            <h2>Supervisor: <?php echo htmlspecialchars($supervisor_name); ?></h2>
            <table>
                <thead>
                    <tr>
                        <th>Reg No</th>
                        <th>Name</th>
                        <th>Program</th>
                        <th>Status</th>
                        <th>Actions</th>
                        <th>View Progress</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($student['reg_no']); ?></td>
                            <td><?php echo htmlspecialchars($student['student_name']); ?></td>
                            <td><?php echo htmlspecialchars($student['program']); ?></td>
                            <td class="<?php echo strtolower($student['status']); ?>">
                                <i class="<?php echo ($student['status'] === 'pending') ? 'fas fa-clock' : (($student['status'] === 'approved') ? 'fas fa-thumbs-up' : 'fas fa-check-circle'); ?>"></i>
                                <?php echo ucfirst($student['status']); ?>
                            </td>
                            <td>
                                <?php if ($student['status'] === 'approved'): ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="verify_id" value="<?php echo htmlspecialchars($student['student_id']); ?>">
                                        <button type="submit">Verify</button>
                                    </form>
                                <?php else: ?>
                                    <span>N/A</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="view_progress_coordinator.php?id=<?php echo htmlspecialchars($student['student_id']); ?>" class="btn view-progress">View Progress</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endforeach;
    } else {
        echo '<p>No students found for any supervisor.</p>';
    }
    ?>
</div>
    <!-- Add canvas element for the chart -->
    <div class="chart-container">
        <canvas id="studentStatusChart"></canvas>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('studentStatusChart').getContext('2d');
        
        // Replace these values with PHP variables if needed
        var pendingCount = <?php echo json_encode($pendingCount); ?>;
        var approvedCount = <?php echo json_encode($approvedCount); ?>;
        var verifiedCount = <?php echo json_encode($verifiedCount); ?>;
        
        new Chart(ctx, {
            type: 'bar', // You can also use 'pie', 'line', etc.
            data: {
                labels: ['Pending', 'Approved', 'Verified'],
                datasets: [{
                    label: 'Number of Students',
                    data: [pendingCount, approvedCount, verifiedCount],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(75, 192, 192, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(75, 192, 192, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
</body>
</html>
