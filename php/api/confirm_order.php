<?php
require_once '../includes/db.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || empty($data['items'])) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty']);
    exit;
}

// Using OOP OrderManager
$result = $orderManager->confirmOrder($data, $_SESSION['user_id'] ?? null);

echo json_encode($result);
?>
