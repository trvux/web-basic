<?php
require_once __DIR__ . '/../../config/db.php';
require_admin();

// DELETE: chỉ nhận POST. Sản phẩm thuộc danh mục này sẽ có category_id = NULL nhờ ON DELETE SET NULL
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('DELETE FROM categories WHERE id = ?');
    $stmt->execute([(int)($_POST['id'] ?? 0)]);
}

header('Location: index.php');
exit;
