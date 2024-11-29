<?php
require 'config.php';

if (isset($_POST['room_id'])) {
    $room_id = $_POST['room_id'];
    $stmt = $pdo->prepare("INSERT INTO rooms (room_id) VALUES (:room_id)");
    $stmt->execute(['room_id' => $room_id]);
    echo json_encode(['success' => true, 'room_id' => $room_id]);
}
