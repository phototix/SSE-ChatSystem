<?php
require_once '../configs/database.php';

try {
    $pdo = connectDatabase();

    // Check and add `timestamp` column if it doesn't exist
    $alterMessagesTable = "
        ALTER TABLE messages
        ADD COLUMN IF NOT EXISTS `timestamp` BIGINT NOT NULL DEFAULT UNIX_TIMESTAMP()
    ";
    $pdo->exec($alterMessagesTable);

    // Optional: Create messages table if it doesn't exist
    $createMessagesTable = "
        CREATE TABLE IF NOT EXISTS messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            room VARCHAR(255) NOT NULL,
            sender VARCHAR(255) NOT NULL,
            text TEXT NOT NULL,
            timestamp BIGINT NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    $pdo->exec($createMessagesTable);

    echo "Database schema update completed successfully.";
} catch (PDOException $e) {
    echo "Error updating database schema: " . $e->getMessage();
}
