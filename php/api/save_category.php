<?php
require_once '../includes/db.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || empty($data['name'])) {
    echo json_encode(['success' => false, 'message' => 'Missing data']);
    exit;
}

try {
    if (!empty($data['id'])) {
        // Update
        $stmt = $pdo->prepare("UPDATE categories SET name = ?, type = ? WHERE id = ?");
        $stmt->execute([$data['name'], $data['type'] ?? 'FOOD', $data['id']]);
    } else {
        // Insert
        $stmt = $pdo->prepare("INSERT INTO categories (name, type) VALUES (?, ?)");
        $stmt->execute([$data['name'], $data['type'] ?? 'FOOD']);
    }
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
