<?php
require_once '../includes/db.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || empty($data['name_ar']) || empty($data['category_id']) || empty($data['price'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

try {
    if (!empty($data['id'])) {
        // Update
        $stmt = $pdo->prepare("UPDATE products SET category_id = ?, name = ?, name_ar = ?, price = ?, ingredients = ? WHERE id = ?");
        $stmt->execute([
            $data['category_id'],
            $data['name'] ?? '',
            $data['name_ar'],
            $data['price'],
            $data['ingredients'] ?? '',
            $data['id']
        ]);
    } else {
        // Insert
        $stmt = $pdo->prepare("INSERT INTO products (category_id, name, name_ar, price, ingredients) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['category_id'],
            $data['name'] ?? '',
            $data['name_ar'],
            $data['price'],
            $data['ingredients'] ?? ''
        ]);
    }
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
