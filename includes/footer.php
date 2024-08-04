<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Footer Example</title>
    <style>
        html, body {
    height: 100%;
    margin: 0;
    padding: 0;
}

body {
    display: flex;
    flex-direction: column;
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    color: #333;
}

.content {
    flex: 1;
    padding: 20px;
    /* Your content styles */
}

footer {
    border: solid black;
    text-align: center;
    padding: 10px;
    background-color: #333;
    color: #fff;
    display: grid;
}

    </style>
</head>
<body>
    <div class="content">
        <!-- Your page content goes here -->
    </div>
    <footer>
        <p>&copy; <?php echo date("Y") ?> MUST Project Management System</p>
    </footer>
    <script src="js/scripts.js"></script>
</body>
</html>
