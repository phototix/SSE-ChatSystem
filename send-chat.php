<?php
// Include the database connection file
$pdo = require 'database.php';

// Get JSON data from the POST request
$data = json_decode(file_get_contents('php://input'), true);

// Extract values
$roomId = $data['room_id'];
$message = $data['message'];
$senderType = $data['sender_type'];

// Insert the new chat message into the database
$stmt = $pdo->prepare('INSERT INTO chat_messages (room_id, sender_type, message) VALUES (?, ?, ?)');
$stmt->execute([$roomId, $senderType, $message]);

echo json_encode(['status' => 'success']);
?>
