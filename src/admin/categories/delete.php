<?php
require_once __DIR__ . '/../../includes/admin_header.php';
// Chỉ xử lý khi có request POST gửi lên id
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    
    // Lưu ý: Nếu bảng products có liên kết khóa ngoại tới categories, 
    // bạn cần xử lý (ví dụ: không cho xóa, hoặc chuyển sản phẩm sang danh mục khác) trước khi xóa.
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id]);
}

// Xóa xong thì quay về trang danh sách
header("Location: index.php");
exit;