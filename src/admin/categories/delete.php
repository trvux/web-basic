<?php
require_once __DIR__ . '/../../config/db.php';

// BẮT BUỘC: Thêm dòng này để chặn người lạ không đăng nhập mà vẫn xóa được
require_admin(); 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    // Ép kiểu (int) để bảo mật
    $id = (int)$_POST['id'];
    
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: index.php");
exit;