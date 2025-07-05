<?php
$host = 'localhost';
$dbname = 'studentInfo';
$username = 'root'; // Default for XAMPP/WAMP
$password = '';     // Default for XAMPP/WAMP

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Enable error mode
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
