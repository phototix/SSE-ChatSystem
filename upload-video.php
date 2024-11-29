<?php
// Function to delete files older than 2 minutes (120 seconds)
function deleteOldFiles($directory) {
    // Get all files in the directory (excluding subdirectories)
    $files = glob("$directory/*");

    foreach ($files as $file) {
        // Skip index.php and check if the file is older than 2 minutes
        if (basename($file) === 'index.php') {
            continue; // Skip the index.php file
        }

        // Check if the file is older than 2 minutes (120 seconds)
        if (time() - filemtime($file) > 120) {
            // Delete the file
            unlink($file);
        }
    }
}

function convertVideo($inputFile, $outputFile) {
    // Command to convert MP4 to WebM (adjust paths based on server setup)
    $command = "ffmpeg -i $inputFile -c:v vp8 -c:a libvorbis $outputFile";
    
    // Execute the command and capture the output (errors and success)
    $output = shell_exec($command);

    // Check if the conversion was successful (empty output means no error, otherwise error)
    if ($output === null) {
        echo "Conversion successful: $outputFile";
    } else {
        echo "Error during conversion: $output";
    }
}

// Call the function to delete old files
deleteOldFiles('uploads');

// Handle the file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roomId = $_POST['room_id'];
    $chunk = $_FILES['chunk'];

    // Generate a unique file name based on roomId and current timestamp
    $filePath = "uploads/$roomId-" . time() . ".webm";
    
    // Move the uploaded chunk to the target file path
    if (move_uploaded_file($chunk['tmp_name'], $filePath)) {
        // Once the chunk is uploaded, proceed with the video conversion
        $outputFile = "uploads/$roomId-" . time() . "-converted.webm"; // Define output file path for the converted video
        convertVideo($filePath, $outputFile); // Convert the video

        // Optionally, you can delete the original video after conversion
        // unlink($filePath); // Uncomment if you want to delete the original file after conversion

        // Send a success response
        echo json_encode(['status' => 'success', 'message' => 'File uploaded and converted successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'File upload failed']);
    }
}
?>
