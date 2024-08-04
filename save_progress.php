<?php include 'db/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $comment = $_POST['comment'];
    $date = date('Y-m-d');

    $stmt = $conn->prepare("INSERT INTO supervisor_comments (student_id, date, comment) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $student_id, $date, $comment);
    $stmt->execute();
    $stmt->close();
    header('Location: supervisor_dashboard.php');
}
?>
