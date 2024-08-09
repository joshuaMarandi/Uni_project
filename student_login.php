<?php
session_start();
require 'db/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reg_no = $_POST['reg_no'];
    $password = $_POST['password'];

    // Check credentials
    $stmt = $conn->prepare("SELECT * FROM students WHERE reg_no = ?");
    $stmt->bind_param('s', $reg_no);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
        if (password_verify($password, $student['password'])) {
            $_SESSION['student_id'] = $student['id'];
            $_SESSION['reg_no'] = $student['reg_no'];
            header('Location: student_panel.php');
            exit();
        } else {
            $error = 'Invalid password';
        }
    } else {
        $error = 'Invalid registration number';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #0056b3;
        }

        .error {
            color: #ff0000;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Student Login</h1>
        <form method="POST" action="">
            <div class="form-group">
                <label for="reg_no">Registration Number:</label>
                <input type="text" id="reg_no" name="reg_no" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
