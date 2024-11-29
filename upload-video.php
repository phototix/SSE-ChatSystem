<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roomId = $_POST['room_id'];
    $chunk = $_FILES['chunk'];
    $filePath = "uploads/$roomId-" . time() . ".webm";
    move_uploaded_file($chunk['tmp_name'], $filePath);
    echo json_encode(['status' => 'success']);
}
?>
