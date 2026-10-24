<?php
require_once __DIR__ . '/../../config/db.php';
require_admin();

// DELETE (sản phẩm thuộc danh mục này sẽ có category_id = NULL nhờ ON DELETE SET NULL)
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('DELETE FROM categories WHERE id = ?');
$stmt->execute([$id]);

header('Location: index.php');
exit;
