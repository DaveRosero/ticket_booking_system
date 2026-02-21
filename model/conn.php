<?php
define('APP_DEBUG', true); // Toggle for error handling 
date_default_timezone_set('Asia/Manila');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function database()
{
    $host = "localhost";
    $user = "root";
    $pw = "";
    $db = "ticket_system";
    $charset = "utf8mb4";

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

    try {
        $pdo = new PDO($dsn, $user, $pw, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);

        return $pdo;
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}
?>