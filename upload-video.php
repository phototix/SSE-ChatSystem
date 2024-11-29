<?php
// Function to delete files older than 2 minutes (120 seconds)
function deleteOldFiles($directory) {
    $files = glob("$directory/*.mp4"); // Match all .webm files in the directory

    foreach ($files as $file) {
        // Check if the file is older than 2 minutes (120 seconds)
        if (time() - filemtime($file) > 120) {
            // Delete the file
            unlink($file);
        }
    }
}

// Call the function to delete old files
deleteOldFiles('uploads');

// Handle the file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roomId = $_POST['room_id'];
    $chunk = $_FILES['chunk'];

    // Generate a unique file name based on roomId and current timestamp
    $filePath = "uploads/$roomId-" . time() . ".mp4";

    // Move the uploaded chunk to the target file path
    if (move_uploaded_file($chunk['tmp_name'], $filePath)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'File upload failed']);
    }
}
?>
