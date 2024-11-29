<?php
// Include the database connection file
$pdo = require 'database.php';

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

$roomId = $_GET['room_id'];
$lastId = 0;

while (true) {
    // Fetch new messages
    $stmt = $pdo->prepare('SELECT * FROM chat_messages WHERE room_id = ? AND id > ? ORDER BY id ASC');
    $stmt->execute([$roomId, $lastId]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($messages as $message) {
        echo "data: " . json_encode($message) . "\n\n";
        $lastId = $message['id'];
    }

    ob_flush();
    flush();
    sleep(1);
}
?>
