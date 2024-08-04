<?/*php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'supervisor') {
    header('Location: login_form.php');
    exit();
}*/
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="stylesheet" href="styles.css">  -->
    <style>
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

.navbar a:hover {
    text-decoration: underline;
}

    </style>
    <title>Supervisor Dashboard</title>
</head>
<body>
    <div class="navbar">
        <a href="supervisor_dashboard.php">Home</a>
        <a href="supervisor_dashboard.php">Supervisors</a>
        <a href="logout.php">Logout</a>
    </div>
</body>
</html>
