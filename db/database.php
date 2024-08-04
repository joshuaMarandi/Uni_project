<?php
$servername = getenv('DB_HOSTNAME');
$username = getenv('DB_USERNAME');
$password = getenv('DB_PASSWORD');
$dbname = getenv('DB_NAME');

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_errno) {
    die("Connection failed: " . $conn->connect_error);
}
// echo "Connected successfully";
?>
