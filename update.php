<?php
require_once 'configs/database.php';

try {
    $pdo = connectDatabase();

    // Drop the `messages` table if it exists
    $dropTableQuery = "DROP TABLE IF EXISTS messages";
    $pdo->exec($dropTableQuery);

    // Create the `messages` table
    $createMessagesTable = "
        CREATE TABLE messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            room VARCHAR(255) NOT NULL,
            sender VARCHAR(255) NOT NULL,
            text TEXT NOT NULL,
            timestamp BIGINT NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    $pdo->exec($createMessagesTable);

    echo "Database schema recreated successfully.";
} catch (PDOException $e) {
    echo "Error updating database schema: " . $e->getMessage();
}
