<?php
// database.php

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'chat_system');
define('DB_USER', 'root');
define('DB_PASS', '#Abccy1982#');

try {
    // Establish a PDO connection
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set the character set to UTF-8
    $pdo->exec("SET NAMES 'utf8'");

} catch (PDOException $e) {
    // Handle connection error
    die("Database connection failed: " . $e->getMessage());
}

// Return the PDO instance
return $pdo;
?>
