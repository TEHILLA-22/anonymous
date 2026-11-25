<?php
// Simple database connection - but still secure
$db_host = 'localhost';
$db_name = 'tehillz_anonymous';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // User-friendly error message
    die("Oops! We're having trouble connecting right now. Please try again in a few minutes.");
}
?>