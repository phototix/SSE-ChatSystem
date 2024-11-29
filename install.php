<?php
// Include the database configuration file
include('configs/database.php');

// Step 1: Check if the database exists
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    echo "Database '$dbname' is ready.\n";
} else {
    echo "Error creating database: " . $conn->error;
    exit();
}

// Select the database to work with
$conn->select_db($dbname);

// Step 2: Create the 'messages' table if it does not exist
$table_sql = "
    CREATE TABLE IF NOT EXISTS messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
";

if ($conn->query($table_sql) === TRUE) {
    echo "Table 'messages' is ready.\n";
} else {
    echo "Error creating table: " . $conn->error;
    exit();
}

// Close the connection
$conn->close();
?>
