<?php
include('../configs/database.php');

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

while (true) {
    // Get the latest messages from the database
    $result = $conn->query("SELECT * FROM messages ORDER BY created_at DESC LIMIT 1");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $data = json_encode($row);

        echo "data: $data\n\n";
        flush();
    }
    
    sleep(1);  // Wait for 1 second before checking for new messages
}
?>
