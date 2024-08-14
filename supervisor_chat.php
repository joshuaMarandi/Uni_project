<?php
session_start();
require 'db/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'supervisor') {
    die("You must be logged in as a supervisor to view this page.");
}

$supervisor_id = $_SESSION['user_id'];
$student_id = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;

// include 'chat_process.php'; // Include the common chat functionality

// session_start();
// require 'db/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $message = $conn->real_escape_string($_POST['message']);
    $sender_type = isset($_SESSION['student_id']) ? 'student' : 'supervisor';

    // Fetch supervisor_id for the student
    $stmt = $conn->prepare("SELECT supervisor_id FROM students WHERE id = ?");
    $stmt->bind_param('i', $student_id);
    $stmt->execute();
    $stmt->bind_result($supervisor_id);
    $stmt->fetch();
    $stmt->close();

    if ($supervisor_id) {
        // Insert the message into the messages table
        $query = "INSERT INTO messages (student_id, supervisor_id, message, sender_type) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('iiss', $student_id, $supervisor_id, $message, $sender_type);
        $stmt->execute();
        $stmt->close();

        header("Location: {$_SERVER['HTTP_REFERER']}");
        exit();
    } else {
        die("Supervisor ID is not assigned for this student.");
    }
}
// Fetch messages
$stmt = $conn->prepare("SELECT * FROM messages WHERE student_id = ? AND supervisor_id = ? ORDER BY created_at ASC");
$stmt->bind_param('ii', $student_id, $supervisor_id);
$stmt->execute();
$result = $stmt->get_result();
$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervisor - Chat with Student</title>
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

        .chat-container {
            width: 500px;
            max-width: 90%;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            height: 80vh;
        }

        .chat-header {
            background-color: #007bff;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        .chat-history {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            border-bottom: 1px solid #ddd;
            background-color: #fafafa;
        }

        .message {
            display: flex;
            margin-bottom: 15px;
        }

        .message .text {
            padding: 10px 15px;
            border-radius: 10px;
            max-width: 70%;
            word-wrap: break-word;
        }

        .message.sent .text {
            margin-left: auto;
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .message.received .text {
            margin-right: auto;
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .chat-form {
            display: flex;
            padding: 10px;
            background: #f1f1f1;
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
        }

        .chat-form textarea {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            resize: none;
            font-size: 16px;
        }

        .chat-form button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-left: 10px;
        }

        .chat-form button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">Chat with Student</div>
        <div class="chat-history" id="chat-history">
            <?php foreach ($messages as $msg): ?>
                <div class="message <?= $msg['sender_type'] === 'supervisor' ? 'sent' : 'received' ?>">
                    <div class="text"><?= htmlspecialchars($msg['message']) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        <form id="chat-form" class="chat-form" method="POST">
            <textarea id="message-input" name="message" placeholder="Type your message..."></textarea>
            <input type="hidden" name="student_id" value="<?= htmlspecialchars($student_id) ?>">
            <button type="submit">Send</button>
        </form>
    </div>

    <script>
        const chatHistory = document.getElementById('chat-history');
        const chatForm = document.getElementById('chat-form');
        const messageInput = document.getElementById('message-input');
        const student_id = <?= json_encode($student_id) ?>;

        chatForm.addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(chatForm);
            fetch('', {
                method: 'POST',
                body: formData
            }).then(() => {
                messageInput.value = '';
                loadMessages();
            });
        });

        function loadMessages() {
            fetch(`?student_id=${student_id}`).then(response => response.text()).then(data => {
                chatHistory.innerHTML = new DOMParser().parseFromString(data, 'text/html').getElementById('chat-history').innerHTML;
                chatHistory.scrollTop = chatHistory.scrollHeight;
            });
        }
        chatHistory.scrollTop = chatHistory.scrollHeight;
    </script>
</body>
</html>
