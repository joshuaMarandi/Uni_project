<?php
include 'db/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'coordinator') {
    header('Location: login_form.php');
    exit();
}

if (isset($_POST['reg_no'])) {
    $reg_no = $_POST['reg_no'];

    // Update student status to 'approved'
    $query = "UPDATE students SET status = 'approved' WHERE reg_no = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $reg_no);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
    
    $stmt->close();
    $conn->close();
}
?>
