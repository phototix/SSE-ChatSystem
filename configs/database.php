<?php
$servername = "localhost";
$username = "root";  // Change as per your setup
$password = "#Abccy1982#";  // Change as per your setup
$dbname = "chat_system";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
