<?php
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

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $phoneNumber = $_POST['phone_number']; // Phone number provided by the coordinator

    // Add your database connection and supervisor insertion logic here
    // For example:
    // $stmt = $conn->prepare("INSERT INTO supervisors (username, password, phone_number) VALUES (?, ?, ?)");
    // $stmt->bind_param('sss', $username, $password, $phoneNumber);
    // $stmt->execute();
    // $stmt->close();

    $message = "Hello $username, you have been added as a supervisor and your login password is $password. Welcome!";
    if (sendSMS($phoneNumber, $message)) {
        echo 'Supervisor added and SMS sent successfully.';
    } else {
        echo 'Supervisor added but failed to send SMS.';
    }
}
?>
