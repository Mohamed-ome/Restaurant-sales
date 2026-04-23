<?php

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $host = 'localhost';
        $dbname = 'al_mantiqa_db';
        $username = 'root';
        $password = '';

        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("خطأ في الاتصال بقاعدة البيانات: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance->pdo;
    }
}

class ProductManager {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAllProducts() {
        return $this->db->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY c.name, p.name_ar")->fetchAll();
    }

    public function getProductById($id) {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function updatePrice($id, $price) {
        $stmt = $this->db->prepare("UPDATE products SET price = ? WHERE id = ?");
        return $stmt->execute([$price, $id]);
    }

    public function saveProduct($data) {
        if (!empty($data['id'])) {
            // Update
            $stmt = $this->db->prepare("UPDATE products SET name_ar = ?, category_id = ?, price = ?, name = ?, ingredients = ? WHERE id = ?");
            return $stmt->execute([$data['name_ar'], $data['category_id'], $data['price'], $data['name'], $data['ingredients'], $data['id']]);
        } else {
            // Insert
            $stmt = $this->db->prepare("INSERT INTO products (name_ar, category_id, price, name, ingredients) VALUES (?, ?, ?, ?, ?)");
            return $stmt->execute([$data['name_ar'], $data['category_id'], $data['price'], $data['name'], $data['ingredients']]);
        }
    }
}

class OrderManager {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function confirmOrder($orderData, $userId) {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("INSERT INTO orders (user_id, total_amount, payment_method, transaction_id, dining_option, notes) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $userId,
                $orderData['total'],
                $orderData['payment_method'],
                $orderData['transaction_id'],
                $orderData['dining_option'],
                $orderData['notes']
            ]);

            $orderId = $this->db->lastInsertId();

            $itemStmt = $this->db->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_at_time) VALUES (?, ?, ?, ?)");
            $stockStmt = $this->db->prepare("UPDATE products SET in_stock = in_stock - ? WHERE id = ?");

            foreach ($orderData['items'] as $item) {
                $itemStmt->execute([$orderId, $item['id'], $item['quantity'], $item['price']]);
                $stockStmt->execute([$item['quantity'], $item['id']]);
            }

            $this->db->commit();
            return ['success' => true, 'order_id' => $orderId];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
