<?php
require_once __DIR__ . '/../../config/db.php';

// Lỗi 1: Thêm dòng này để bắt buộc phải đăng nhập admin mới được xóa
require_admin(); 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    // Lỗi 4: Ép kiểu (int) để bảo mật
    $id = (int)$_POST['id'];
    
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: index.php");
exit;