<?php
session_start();
require 'db/database.php'; // Include your database connection

// Check if user is logged in and has coordinator role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'coordinator') {
    die("Unauthorized access");
}

// Get the coordinator's ID
$coordinator_id = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reg_no = $_POST['reg_no'];
    $name = $_POST['name'];
    $phone_no = $_POST['phone_no'];
    $program = $_POST['program'];
    $project_title = $_POST['project_title'];
    $academic_year = $_POST['academic_year'];
    $supervisor_id = $_POST['supervisor_id'];
    $password = $_POST['password'];

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert new student into the students table
    $stmt = $conn->prepare("INSERT INTO students (reg_no, name, phone_no, program, project_title, academic_year, supervisor_id, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssis", $reg_no, $name, $phone_no, $program, $project_title, $academic_year, $supervisor_id, $hashed_password);

    if ($stmt->execute()) {
        echo "Student added successfully.";
    } else {
        echo "Error adding student: " . $stmt->error;
    }

    $stmt->close();
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
            margin: 20px;
            padding: 0;
        }

        h1 {
            text-align: center;
        }

        form {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="password"],
        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h1>Add Student</h1>
    <form action="add_student.php" method="post">
        <label for="reg_no">Registration Number:</label>
        <input type="text" id="reg_no" name="reg_no" required>

        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="phone_no">Phone Number:</label>
        <input type="text" id="phone_no" name="phone_no" required>

        <label for="program">Program:</label>
        <input type="text" id="program" name="program" required>

        <label for="project_title">Project Title:</label>
        <input type="text" id="project_title" name="project_title" required>

        <label for="academic_year">Academic Year:</label>
        <input type="text" id="academic_year" name="academic_year" required>

        <label for="supervisor">Supervisor:</label>
        <select id="supervisor" name="supervisor_id" required>
            <option value="">Select a supervisor</option>
            <?php
            // Fetch supervisors allocated by the specific coordinator
            $stmt = $conn->prepare("SELECT id, username AS name FROM users WHERE role = 'supervisor' AND added_by_coordinator_id = ?");
            $stmt->bind_param("i", $coordinator_id);
            $stmt->execute();
            $result = $stmt->get_result();

            // Generate the options for the supervisor dropdown
            while ($row = $result->fetch_assoc()) {
                echo "<option value=\"{$row['id']}\">{$row['name']}</option>";
            }

            $stmt->close();
            ?>
        </select>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <input type="submit" value="Add Student">
    </form>
</body>
</html>
