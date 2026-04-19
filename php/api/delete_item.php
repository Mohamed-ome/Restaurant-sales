<?php
require_once '../includes/db.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || empty($data['type']) || empty($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing data']);
    exit;
}

try {
    if ($data['type'] === 'category') {
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    } else {
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    }
    $stmt->execute([$data['id']]);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    if(str_contains($e->getMessage(), 'foreign key constraint')) {
        echo json_encode(['success' => false, 'message' => 'لا يمكن حذف هذا البند لوجود مرتبطة به']);
    } else {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
