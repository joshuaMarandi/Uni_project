<?php
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
    <title>Chat</title>
    <style>
        /* Add CSS similar to previous chat interface */
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-history">
            <?php foreach ($messages as $msg): ?>
                <div class="message <?= $msg['sender_type'] === 'supervisor' ? 'sent' : 'received' ?>">
                    <div class="text"><?= htmlspecialchars($msg['message']) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        <form method="POST" action="send_message.php">
            <textarea name="message"></textarea>
            <input type="hidden" name="student_id" value="<?= htmlspecialchars($student_id) ?>">
            <button type="submit">Send</button>
        </form>
    </div>
</body>
</html>
