<?php
// Database configuration
$host = 'localhost'; // Or use 127.0.0.1
$db_name = 'admin_p2p'; // Your database name
$db_user = 'admin_admin'; // Your database username
$db_password = 'Qwasa1234'; // Your database password

try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $db_user, $db_password);
    // Set PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Database connection successful.";
} catch (PDOException $e) {
    // Handle connection error
    die("Database connection failed: " . $e->getMessage());
}
