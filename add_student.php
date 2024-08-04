<?//php include 'includes/header.php'; ?>
<?php include 'db/database.php'; ?>

<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'coordinator') {
    header('Location: login_form.php');
    exit();
}

// Fetch all supervisors
$supervisors = $conn->query("SELECT * FROM users WHERE role = 'supervisor'");

// Add student to supervisor
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_student'])) {
    $student_reg_no = $_POST['student_reg_no'];
    $student_name = $_POST['student_name'];
    $student_program = $_POST['student_program'];
    $supervisor_id = $_POST['supervisor_id'];

    // Insert the student into the students table with the selected supervisor
    $stmt = $conn->prepare("INSERT INTO students (reg_no, name, program, supervisor_id) VALUES (?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param('sssi', $student_reg_no, $student_name, $student_program, $supervisor_id);
        $stmt->execute();
        $stmt->close();
        echo "Student added successfully.";
    } else {
        echo "Failed to add student: " . $conn->error;
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
        }

        .container {
            padding: 20px;
            max-width: 600px;
            margin: auto;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"], select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }

        button {
            padding: 10px 15px;
            background-color: #333;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #555;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Add Student</h1>
    <form method="POST" action="add_student.php">
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
            <label for="supervisor_id">Supervisor:</label>
            <select id="supervisor_id" name="supervisor_id" required>
                <?php while ($row = $supervisors->fetch_assoc()): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo $row['username']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" name="add_student">Add Student</button>
    </form>
</div>
</body>
</html>
