<?php
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && is_array($_POST['qty'] ?? null)) {
    foreach ($_POST['qty'] as $id => $qty) {
        $id = (int)$id;
        $qty = (int)$qty;
        $stmt = $pdo->prepare('SELECT stock FROM products WHERE id = ?');
        $stmt->execute([$id]);
        $stock = $stmt->fetchColumn();
        if ($qty <= 0 || $stock === false || $stock <= 0) {
            unset($_SESSION['cart'][$id]);
        } else {
            if ($qty > $stock) {
                $_SESSION['cart_message'] = 'Số lượng đã giảm về đúng số còn trong kho.';
            }
            $_SESSION['cart'][$id] = min($qty, (int)$stock);
        }
    }
}

header('Location: index.php');
exit;
