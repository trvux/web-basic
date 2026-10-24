<?php
require_once __DIR__ . '/../../config/db.php';
require_admin();

// DELETE (đơn giản cho môn cơ bản; thực tế nên dùng POST thay vì GET)
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
$stmt->execute([$id]);

header('Location: index.php');
exit;
