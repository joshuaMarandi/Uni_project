<?php
session_start();
require 'db/database.php';

// Check if the session is set and role is coordinator
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'coordinator') {
    header('Location: login_form.php');
    exit();
}

// Fetch supervisors added by the current coordinator
$coordinator_id = $_SESSION['user_id'];
$supervisors = $conn->prepare("SELECT * FROM users WHERE role = 'supervisor' AND added_by_coordinator_id = ?");
$supervisors->bind_param('i', $coordinator_id);
$supervisors->execute();
$supervisor_result = $supervisors->get_result();

// Add student to supervisor
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_student'])) {
    // Sanitize and validate input data
    $student_reg_no = filter_var(trim($_POST['student_reg_no']), FILTER_SANITIZE_STRING);
    $student_name = filter_var(trim($_POST['student_name']), FILTER_SANITIZE_STRING);
    $student_program = filter_var(trim($_POST['student_program']), FILTER_SANITIZE_STRING);
    $student_phone_no = filter_var(trim($_POST['phone_no']), FILTER_SANITIZE_STRING);
    $student_project_title = filter_var(trim($_POST['project_title']), FILTER_SANITIZE_STRING);
    $student_academic_year = filter_var(trim($_POST['academic_year']), FILTER_SANITIZE_STRING);
    $student_progress = filter_var(trim($_POST['progress']), FILTER_SANITIZE_STRING);
    $supervisor_id = filter_var($_POST['supervisor_id'], FILTER_VALIDATE_INT);
    $password = filter_var(trim($_POST['password']), FILTER_SANITIZE_STRING);

    if ($supervisor_id === false) {
        echo "Invalid supervisor selection.";
    } else {
        // Hash the password before storing it
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Prepare and execute the SQL statement
        $stmt = $conn->prepare("INSERT INTO students (reg_no, name, program, phone_no, project_title, academic_year, supervisor_id, progress, status, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?)");
        if ($stmt === false) {
            echo "Failed to prepare statement: " . htmlspecialchars($conn->error);
        } else {
            $stmt->bind_param('sssssssss', $student_reg_no, $student_name, $student_program, $student_phone_no, $student_project_title, $student_academic_year, $supervisor_id, $student_progress, $hashed_password);
            if ($stmt->execute()) {
                echo "<p>Student added successfully.</p>";
            } else {
                echo "<p>Failed to add student: " . htmlspecialchars($stmt->error) . "</p>";
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            background-color: #f4f4f4;
        }

        .container {
            padding: 20px;
            max-width: 600px;
            margin: auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"], input[type="password"], input[type="number"], select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            padding: 10px 15px;
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

        p {
            color: #333;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Add Student</h1>
    <form method="POST" action="">
        <div class="form-group">
            <label for="student_reg_no">Student Registration Number:</label>
            <input type="text" id="student_reg_no" name="student_reg_no" required>
        </div>
        <div class="form-group">
            <label for="student_name">Student Name:</label>
            <input type="text" id="student_name" name="student_name" required>
        </div>
        <div class="form-group">
            <label for="student_program">Student Program:</label>
            <input type="text" id="student_program" name="student_program" required>
        </div>
        <div class="form-group">
            <label for="phone_no">Phone Number:</label>
            <input type="text" id="phone_no" name="phone_no" required>
        </div>
        <div class="form-group">
            <label for="project_title">Project Title:</label>
            <input type="text" id="project_title" name="project_title" required>
        </div>
        <div class="form-group">
            <label for="academic_year">Academic Year:</label>
            <input type="text" id="academic_year" name="academic_year" required>
        </div>
        <div class="form-group">
            <label for="progress">Progress:</label>
            <input type="text" id="progress" name="progress">
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="supervisor_id">Supervisor:</label>
            <select id="supervisor_id" name="supervisor_id" required>
                <?php while ($row = $supervisor_result->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($row['id']); ?>"><?php echo htmlspecialchars($row['username']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" name="add_student">Add Student</button>
    </form>
</div>
</body>
</html>
