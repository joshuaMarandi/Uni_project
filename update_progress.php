<?//php include 'includes/header.php'; ?>
<?php include 'db/database.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* General Styles */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f7f9fc;
    color: #333;
    margin: 0;
    padding: 0;
    line-height: 1.6;
}

.container {
    max-width: 500px;
    margin: 60px auto;
    padding: 30px;
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    border: 1px solid #e3e6f0;
}

h1, h2 {
    color: #2c3e50;
    text-align: center;
}

h1 {
    margin-bottom: 20px;
    font-size: 24px;
}

h2 {
    margin-top: 0;
    font-size: 18px;
    font-weight: normal;
    color: #34495e;
}

/* Form Styles */
.form-group {
    margin-bottom: 20px;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #4a4a4a;
}

textarea {
    width: 200%;
    /* max-width: 100%; Ensures it doesn't exceed the container width */
    height: 100px; /* Set a fixed height */
    padding: 10px;
    border: 2px solid #ccd1d9;
    border-radius: 4px;
    resize: none; /* Prevents resizing */
    font-family: inherit;
    font-size: 14px;
    background-color: #f4f4f9;
    color: #5d5d5d;
}

/* Button Styles */
.btn {
    display: inline-block;
    width: 100%;
    padding: 12px;
    background-color: #3498db;
    color: #ffffff;
    text-align: center;
    border: none;
    border-radius: 4px;
    text-decoration: none;
    cursor: pointer;
    font-size: 16px;
    font-weight: 600;
    transition: background-color 0.3s ease;
}

.btn:hover {
    background-color: #2980b9;
}

/* Responsive Styles */
@media (max-width: 500px) {
    .container {
        margin: 30px 10px;
        padding: 20px;
    }

    h1 {
        font-size: 22px;
    }

    h2 {
        font-size: 16px;
    }
}

    </style>
    <title>Update Progress</title>
</head>
<body>
<div class="container">
    <h1>Update Student Progress</h1>
    <!-- <a href="supervisor_dashboard.php" class="btn grey" style="width: 12%;">back</a> -->
    <?php
    $student_id = $_GET['id'];
    $result = $conn->query("SELECT * FROM students WHERE id = $student_id");
    $student = $result->fetch_assoc();
    ?>
    <h2><?php echo htmlspecialchars($student['name']); ?> (<?php echo htmlspecialchars($student['reg_no']); ?>)</h2>
    <form action="save_progress.php" method="post">
        <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student_id); ?>">
        <div class="form-group">
            <label for="comment">Progress Comment:</label>
            <textarea id="comment" name="comment" placeholder="write your comment" required></textarea>
        </div>
        <button type="submit" class="btn">Save Progress</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
