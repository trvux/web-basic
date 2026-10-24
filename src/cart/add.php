<?php
require_once __DIR__ . '/../config/db.php';

// Giỏ hàng lưu trong session: $_SESSION['cart'] = [product_id => số lượng]
$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT stock FROM products WHERE id = ?');
$stmt->execute([$id]);
$product = $stmt->fetch();

if ($product && $product['stock'] > 0) {
    $current = $_SESSION['cart'][$id] ?? 0;
    // không cho thêm vượt quá tồn kho
    $_SESSION['cart'][$id] = min($current + 1, $product['stock']);
}

header('Location: index.php');
exit;
