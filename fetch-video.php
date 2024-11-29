<?php
// fetch-video.php

// Ensure the room_id is passed correctly
$roomId = $_GET['room_id'];

// Get the list of video files for the room
$files = glob("uploads/$roomId-*.webm"); // Adjust extension to your file type

if (count($files) > 0) {
    // Sort files by modification time
    usort($files, function($a, $b) {
        return filemtime($b) - filemtime($a);
    });

    $latestFile = $files[0];

    // Check if the latest file exists
    if (file_exists($latestFile)) {
        header('Content-Type: video/webm');
        readfile($latestFile); // Serve the video file
    } else {
        http_response_code(404);
        echo "Video file not found.";
    }
} else {
    http_response_code(404);
    echo "No video files available.";
}
?>
