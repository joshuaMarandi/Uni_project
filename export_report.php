<?php
include 'db/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'coordinator') {
    header('Location: login_form.php');
    exit();
}

$coordinator_id = $_SESSION['user_id'];

// Default category is 'not_verified'
$category = isset($_GET['category']) ? $_GET['category'] : 'not_verified';

// Validate category
if (!in_array($category, ['verified', 'not_verified'])) {
    die('Invalid category');
}

// Fetch students based on the category
$query = "
    SELECT u.username AS supervisor_name, s.id AS student_id, s.reg_no, s.name AS student_name, s.program, s.status
    FROM students s
    INNER JOIN users u ON s.supervisor_id = u.id
    WHERE u.added_by_coordinator_id = ? AND s.status = ?
    ORDER BY u.username, s.reg_no
";

$stmt = $conn->prepare($query);
$category_status = $category === 'verified' ? 'verified' : 'pending';
$stmt->bind_param('is', $coordinator_id, $category_status);
$stmt->execute();
$result = $stmt->get_result();

// Check if any rows are returned
if ($result->num_rows > 0) {
    // Output CSV headers
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="students_' . $category . '.csv"');

    $output = fopen('php://output', 'w');

    // Column headings
    fputcsv($output, ['Supervisor Name', 'Student ID', 'Reg No', 'Name', 'Program', 'Status']);

    // Fetch rows and write to CSV
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }

    fclose($output);
    exit();
} else {
    echo 'No data available for this category.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coordinator Dashboard</title>
    <style>
        /* Existing styles here */
        
        .btn-export {
            background-color: #17a2b8; /* Teal color */
        }

        .btn-export:hover {
            background-color: #138496; /* Darker teal */
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Coordinator Dashboard</h1>
    
    <!-- Add Export CSV Buttons -->
    <a href="export_report.php?category=verified" class="btn btn-export">Export Verified CSV</a>
    <a href="export_report.php?category=not_verified" class="btn btn-export">Export Not Verified CSV</a>

    <!-- Existing content here -->
    
    <?php
    // Existing PHP code here
    ?>
</div>

</body>
</html>

