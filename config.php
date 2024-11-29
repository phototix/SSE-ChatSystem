<?php

// Database configuration
$host = 'localhost'; // e.g., 'localhost' or RDS endpoint if using AWS RDS
$dbname = 'chat_system'; // e.g., 'video_chat_db'
$username = 'root';
$password = '#Abccy1982#';

// Set up PDO connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set PDO attributes for error handling and charset
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET NAMES 'utf8mb4'");
} catch (PDOException $e) {
    // Handle connection errors
    die("Database connection failed: " . $e->getMessage());
}

?>
