<?php
require_once __DIR__ . '/../config/db.php';

// Giỏ hàng lưu trong session: $_SESSION['cart'] = [product_id => số lượng]
$id = (int)($_GET['id'] ?? 0);
// ?qty= không bắt buộc: không gửi, hoặc gửi số nhỏ hơn 1, thì thêm 1
$qty = max(1, (int)($_GET['qty'] ?? 1));

$stmt = $pdo->prepare('SELECT stock FROM products WHERE id = ?');
$stmt->execute([$id]);
$product = $stmt->fetch();

if ($product && $product['stock'] > 0) {
    $current = $_SESSION['cart'][$id] ?? 0;
    // không cho thêm vượt quá tồn kho
    if ($current + $qty > $product['stock']) {
        $_SESSION['cart_message'] = 'Số lượng đã giảm về đúng số còn trong kho.';
    }
    $_SESSION['cart'][$id] = min($current + $qty, $product['stock']);
}

header('Location: index.php');
exit;
