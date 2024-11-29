<?php
require_once '../configs/database.php';

$room = $_GET['room'] ?? '';
$timestamp = $_GET['timestamp'] ?? 0;

if (!$room) {
    echo json_encode([]);
    exit;
}

try {
    $pdo = connectDatabase();
    $stmt = $pdo->prepare("SELECT * FROM messages WHERE room = :room AND timestamp > :timestamp ORDER BY timestamp ASC");
    $stmt->execute([':room' => $room, ':timestamp' => $timestamp]);

    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($messages);
} catch (PDOException $e) {
    echo json_encode([]);
}
