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

try {
    $pdo->beginTransaction();

    // 1. Create Order
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, payment_method, notes) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $_SESSION['user_id'] ?? null,
        $data['total'],
        $data['payment_method'] ?? 'CASH',
        $data['notes'] ?? null
    ]);
    
    $orderId = $pdo->lastInsertId();

    // 2. Create Order Items & Update Stock
    $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_at_time) VALUES (?, ?, ?, ?)");
    $stockStmt = $pdo->prepare("UPDATE products SET in_stock = in_stock - ? WHERE id = ?");

    foreach ($data['items'] as $item) {
        $itemStmt->execute([
            $orderId,
            $item['id'],
            $item['quantity'],
            $item['price']
        ]);

        $stockStmt->execute([
            $item['quantity'],
            $item['id']
        ]);
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'order_id' => $orderId]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
