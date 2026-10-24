<?php
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['qty'] ?? [] as $id => $qty) {
        $id = (int)$id;
        $qty = (int)$qty;
        if ($qty <= 0) {
            unset($_SESSION['cart'][$id]); // số lượng 0 = xoá khỏi giỏ
        } else {
            $stmt = $pdo->prepare('SELECT stock FROM products WHERE id = ?');
            $stmt->execute([$id]);
            $product = $stmt->fetch();
            if ($product) {
                $_SESSION['cart'][$id] = min($qty, $product['stock']);
            }
        }
    }
}

header('Location: index.php');
exit;
