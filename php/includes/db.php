<?php
// Configuration for XAMPP (Localhost)
$host = 'localhost';
$dbname = 'al_mantiqa_db';
$username = 'root'; // XAMPP default
$password = '';     // XAMPP default

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // In production, log error and show friendly message
    die("خطأ في الاتصال بقاعدة البيانات: " . $e->getMessage());
}

// Global Help Functions
function formatCurrency($amount) {
    return number_format($amount, 2) . ' ر.س';
}
?>
