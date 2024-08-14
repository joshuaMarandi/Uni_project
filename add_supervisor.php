<?php
session_start();
require 'db/database.php';
require 'vendor/autoload.php'; // Path to the autoload file from Composer

use Twilio\Rest\Client;

// Twilio credentials
$sid = 'AC58cbd1b072d11bedae1012d2efd96f40';
$token = 'a74bb2a926c3a6c28184f7d599f49fde';
$twilio_number = '+19388677884';

// Create a Twilio client
$client = new Client($sid, $token);

function sendSMS($to, $message) {
    global $client, $twilio_number;

    try {
        $client->messages->create(
            $to,
            [
                'from' => $twilio_number,
                'body' => $message
            ]
        );
        return true;
    } catch (Exception $e) {
        error_log('Error sending SMS: ' . $e->getMessage());
        return false;
    }
}

// Check if user is logged in and has the coordinator role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'coordinator') {
    header('Location: login_form.php');
    exit();
}

$coordinator_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $phone_number = trim($_POST['phone_number']);

    // Validate input
    if (empty($username) || empty($password) || empty($phone_number)) {
        echo '<p>Please fill in all fields.</p>';
    } else {
        // Hash the passwordd
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare and execute the SQL statement
        $stmt = $conn->prepare("INSERT INTO users (username, password, role, added_by_coordinator_id, phone) VALUES (?, ?, 'supervisor', ?, ?)");
        $stmt->bind_param('ssii', $username, $hashed_password, $coordinator_id, $phone_number);

        if ($stmt->execute()) {
            // Prepare the SMS message
            $message = "Hello $username, you have been added as a supervisor and your login password is $password. Welcome!";

            // Send SMS
            if (sendSMS($phone_number, $message)) {
                echo '<p>Supervisor added and SMS sent successfully.</p>';
            } else {
                echo '<p>Supervisor added but failed to send SMS.</p>';
            }
        } else {
            echo '<p>Failed to add supervisor: ' . $stmt->error . '</p>';
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Supervisor</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Basic Reset */
        body, h1, p, form, a {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }

        .navbar {
            background-color: #343a40; /* Dark grey */
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #fff;
        }

        .navbar a {
            color: #fff;
            text-decoration: none;
            padding: 10px 15px;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .navbar a:hover {
            background-color: #495057; /* Darker grey */
            border-radius: 4px;
        }

        .popup-menu {
            position: relative;
            display: inline-block;
        }

        .popup-menu span {
            cursor: pointer;
            padding: 10px 15px;
            font-size: 16px;
            font-weight: bold;
        }

        .popup-content {
            display: none;
            position: absolute;
            background-color: #343a40; /* Dark grey */
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }

        .popup-content a {
            color: #fff;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            text-align: left;
        }

        .popup-content a:hover {
            background-color: #495057; /* Darker grey */
        }

        .popup-menu:hover .popup-content {
            display: block;
        }

        .container {
            padding: 20px;
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="password"] {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        button {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            color: #fff;
            background-color: #007bff; /* Blue color */
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3; /* Darker blue */
        }

        .btn-back {
            display: inline-block;
            padding: 10px 15px;
            border-radius: 4px;
            color: #fff;
            background-color: #6c757d; /* Grey color */
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
            text-align: center;
        }

        .btn-back:hover {
            background-color: #5a6268; /* Darker grey */
        }

        .btn-back i {
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="coordinators_dashboard.php">Home</a>
        <a href="supervisors.php">Supervisors</a>
        <div class="popup-menu">
            <span>Menu</span>
            <div class="popup-content">
                <a href="add_student.php">Add Student</a>
                <a href="add_supervisor.php">Add Supervisor</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </div>

    <div class="container">
        <h1>Add Supervisor</h1>
        <form method="POST" action="add_supervisor.php">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <br>
            <label for="phone_number">Phone Number:</label>
            <input type="text" id="phone_number" name="phone_number" required>
            <br>
            <button type="submit">Add Supervisor</button>
        </form>
    </div>

    <!-- <a href="supervisors.php" class="btn-back">
        <i class="fas fa-chevron-left"></i> Back
    </a> -->
</body>
</html>
