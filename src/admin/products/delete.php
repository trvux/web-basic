<?php
require_once __DIR__ . '/../../config/db.php';
require_admin();

// DELETE: chỉ nhận POST, để không ai xoá được chỉ bằng cách bấm vào một đường link
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
    $stmt->execute([(int)($_POST['id'] ?? 0)]);
}

header('Location: index.php');
exit;
