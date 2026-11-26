<?php
require_once __DIR__ . '/../../includes/admin_header.php';

// Chỉ xử lý khi có id được gửi lên bằng phương thức POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    
    // Xóa banner khỏi database
    $stmt = $pdo->prepare('DELETE FROM banners WHERE id = ?');
    $stmt->execute([$id]);
}

// Xong thì quay về trang danh sách
header('Location: index.php');
exit;
?>