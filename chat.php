<?php
include "db/database.php";

// Fetch supervisor ID (this would typically come from the logged-in user's session)
$supervisor_id = 1; // Replace with dynamic value

// Fetch the list of students assigned to this supervisor
$students_query = "SELECT id, name FROM students WHERE supervisor_id = '$supervisor_id'";
$students_result = $conn->query($students_query);
$students = [];
while ($row = $students_result->fetch_assoc()) {
    $students[] = $row;
}

// Handle new message submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $message = $conn->real_escape_string($_POST['message']);

    $query = "INSERT INTO messages (student_id, supervisor_id, message) VALUES ('$student_id', '$supervisor_id', '$message')";
    $conn->query($query);
    exit;
}

// Fetch messages for a specific student (used for AJAX)
if (isset($_GET['student_id'])) {
    $student_id = $_GET['student_id'];

    $query = "SELECT * FROM messages WHERE student_id = '$student_id' AND supervisor_id = '$supervisor_id' ORDER BY created_at ASC";
    $result = $conn->query($query);
    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }

    foreach ($messages as $msg) {
        $message_class = ($msg['student_id'] == $student_id) ? 'sent' : 'received';
        echo '<div class="message ' . $message_class . '">';
        echo '<div class="text">' . htmlspecialchars($msg['message']) . '</div>';
        echo '</div>';
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervisor Chat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            height: 100vh;
        }

        .container {
            display: flex;
            width: 100%;
            max-width: 1000px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .student-list {
            width: 300px;
            border-right: 1px solid #ddd;
            padding: 20px;
            background-color: #f7f7f7;
        }

        .student-list h3 {
            margin: 0 0 20px;
        }

        .student-list a {
            display: block;
            padding: 10px;
            margin-bottom: 10px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            text-align: center;
        }

        .student-list a:hover {
            background-color: #0056b3;
        }

        .chat-container {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .chat-history {
            flex: 1;
            overflow-y: scroll;
            padding: 20px;
            border-bottom: 1px solid #ddd;
        }

        .message {
            display: flex;
            margin-bottom: 15px;
        }

        .message .text {
            padding: 10px 15px;
            border-radius: 5px;
            max-width: 70%;
            word-wrap: break-word;
        }

        .message.sent .text {
            margin-left: auto;
            background-color: #d1d1d1;
            color: black;
        }

        .message.received .text {
            margin-right: auto;
            background-color: #007bff;
            color: white;
        }

        .chat-form {
            display: flex;
            padding: 10px;
            background: #f1f1f1;
        }

        .chat-form textarea {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            resize: none;
        }

        .chat-form button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .chat-form button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="student-list">
            <h3>Students</h3>
            <?php foreach ($students as $student): ?>
                <a href="#" data-student-id="<?= $student['id'] ?>"><?= htmlspecialchars($student['name']) ?></a>
            <?php endforeach; ?>
        </div>
        <div class="chat-container">
            <div class="chat-history" id="chat-history">
                <!-- Chat history will be loaded here -->
            </div>
            <form id="chat-form" class="chat-form" method="POST" style="display:none;">
                <textarea id="message-input" name="message" placeholder="Type your message..."></textarea>
                <input type="hidden" name="student_id" id="student_id">
                <button type="submit">Send</button>
            </form>
        </div>
    </div>

    <script>
        const chatHistory = document.getElementById('chat-history');
        const chatForm = document.getElementById('chat-form');
        const messageInput = document.getElementById('message-input');
        const studentIdInput = document.getElementById('student_id');
        const studentLinks = document.querySelectorAll('.student-list a');

        studentLinks.forEach(link => {
            link.addEventListener('click', function(event) {
                event.preventDefault();
                const studentId = this.dataset.studentId;
                studentIdInput.value = studentId;
                chatForm.style.display = 'flex';
                loadMessages(studentId);
            });
        });

        chatForm.addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(chatForm);
            fetch('', {
                method: 'POST',
                body: formData
            }).then(() => {
                messageInput.value = '';
                loadMessages(studentIdInput.value);
            });
        });

        function loadMessages(studentId) {
            fetch(`?student_id=${studentId}`)
                .then(response => response.text())
                .then(data => {
                    chatHistory.innerHTML = data;
                    chatHistory.scrollTop = chatHistory.scrollHeight;
                });
        }
    </script>
</body>
</html>





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
        // Hash the password
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
