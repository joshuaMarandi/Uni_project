<?php include 'db/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reg_no = $_POST['reg_no'];
    $name = $_POST['name'];
    $phone_no = $_POST['phone_no'];
    $program = $_POST['program'];
    $project_title = $_POST['project_title'];
    $academic_year = $_POST['academic_year'];

    $stmt = $conn->prepare("INSERT INTO students (reg_no, name, phone_no, program, project_title, academic_year) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $reg_no, $name, $phone_no, $program, $project_title, $academic_year);
    $stmt->execute();
    $stmt->close();
    header('Location: supervisor_dashboard.php');
}
?>
