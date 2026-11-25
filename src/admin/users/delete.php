<?php
require_once __DIR__ . '/../../config/db.php';
require_admin(); 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $current_user_id = $_SESSION['user_id'] ?? 0; 

    if ($id === $current_user_id) {
        die('Bạn không thể tự xóa tài khoản của chính mình! <a href="index.php">Quay lại</a>');
    }

    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
}
header("Location: index.php");
exit;