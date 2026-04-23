<?php
// Configuration for XAMPP (Localhost)
require_once __DIR__ . '/Classes.php';

$pdo = Database::getInstance();

// Initialize Managers
$productManager = new ProductManager();
$orderManager = new OrderManager();

// Global Help Functions
function formatCurrency($amount) {
    return number_format($amount, 2) . ' ج.س';
}
?>
