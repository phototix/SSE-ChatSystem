<?php
$roomId = $_GET['room_id'];
$files = glob("uploads/$roomId-*.webm");
if ($files) {
    $latestFile = end($files);
    header('Content-Type: video/webm');
    readfile($latestFile);
}
?>