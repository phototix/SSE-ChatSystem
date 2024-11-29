<?php
// fetch-video.php

$roomId = $_GET['room_id'];
$files = glob("uploads/$roomId-*.webm"); // Get all video chunks for this room

if (count($files) > 0) {
    // Sort the files by modification time or another criteria to get the latest chunk
    usort($files, function($a, $b) {
        return filemtime($b) - filemtime($a); // Sort in descending order by modification time
    });

    $latestFile = $files[0];

    // Check if the latest file exists
    if (file_exists($latestFile)) {
        header('Content-Type: video/webm');
        readfile($latestFile);
    } else {
        http_response_code(404);
        echo "Video file not found.";
    }
} else {
    http_response_code(404);
    echo "No video chunks available.";
}
?>
