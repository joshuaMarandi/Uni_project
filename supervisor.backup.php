<?php 
include 'includes/header.php'; 
include 'db/database.php'; 
session_start(); 

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'supervisor') {
    header('Location: login_form.php');
    exit();
}

$supervisor_id = $_SESSION['user_id'];
$sql = "SELECT * FROM students WHERE supervisor_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Failed to prepare SQL statement: " . $conn->error);
}

$stmt->bind_param("i", $supervisor_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container">
    <h1>Supervisor Dashboard</h1>
    <a href="add_student.php" class="btn">Add Student</a>
    <h2>Students List</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Reg No</th>
                <th>Name</th>
                <th>Program</th>
                <th>Project Title</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>{$row['reg_no']}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['program']}</td>
                    <td>{$row['project_title']}</td>
                    <td>
                        <a href='view_progress.php?id={$row['id']}' class='btn'>View Progress</a>
                        <a href='update_progress.php?id={$row['id']}' class='btn'>Update Progress</a>
                    </td>
                </tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
