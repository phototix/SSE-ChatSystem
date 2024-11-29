<?php
// fetch-video.php
if (isset($_GET['room_id'])) {
    $roomId = $_GET['room_id'];
    
    // Ensure the uploaded files are in a valid directory and have the right extension
    $directory = 'uploads/';
    $filePattern = $directory . $roomId . '-*.webm';  // Match all video files for this room
    $files = glob($filePattern);

    if (count($files) > 0) {
        // Select the most recent video chunk (or implement your own chunk fetching logic)
        $fileToServe = end($files); // Get the most recently added file

        // Check if the file exists and is readable
        if (file_exists($fileToServe) && is_readable($fileToServe)) {
            // Send the file to the client as a blob
            header('Content-Type: video/webm');
            header('Content-Length: ' . filesize($fileToServe)); // Set the correct file size
            readfile($fileToServe);
            exit; // Stop the script after serving the file
        } else {
            // Handle file not found or unreadable
            http_response_code(404);
            echo "Video file not found or unreadable.";
            exit;
        }
    } else {
        // Handle no video files found
        http_response_code(404);
        echo "No video files found for this room.";
        exit;
    }
} else {
    http_response_code(400);
    echo "Missing room ID parameter.";
    exit;
}
?>
