<?php
include 'db/database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($user_id, $hashed_password, $role);

    if ($stmt->fetch() && password_verify($password, $hashed_password)) {
        $_SESSION['user_id'] = $user_id;
        $_SESSION['role'] = $role;
        if ($role == 'supervisor') {
            header('Location: supervisor_dashboard.php');
        } else if ($role == 'coordinator') {
            header('Location: coordinator_dashboard.php');
        }
    } else {
        echo "Invalid username or password.";
    }
    $stmt->close();
}
?>
