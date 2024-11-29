<?php
// fetch-video.php
header('Access-Control-Allow-Origin: *');
header('Content-Type: video/mp4');

// Ensure the room_id is passed correctly
$roomId = $_GET['room_id'];

// Get the list of video files for the room
$files = glob("uploads/$roomId-*.mp4"); // Adjust extension to your file type

if (count($files) > 0) {
    // Sort files by modification time
    usort($files, function($a, $b) {
        return filemtime($b) - filemtime($a);
    });

    $latestFile = $files[0];

    // Check if the latest file exists
    if (file_exists($latestFile)) {
        header('Content-Type: video/mp4');
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
