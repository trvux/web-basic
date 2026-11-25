<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/admin_header.php'; 

$error = '';

// Kiểm tra nếu người dùng bấm nút Lưu (gửi form bằng POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    
    if (empty($name)) {
        $error = 'Vui lòng nhập tên danh mục.';
    } else {
        // Dùng prepare() để chống SQL Injection
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
        if ($stmt->execute([$name])) {
            // Thành công thì quay về trang danh sách
            header("Location: index.php");
            exit;
        } else {
            $error = 'Có lỗi xảy ra khi thêm danh mục.';
        }
    }
}
?>

<div class="p-6 max-w-lg mx-auto">
    <h1 class="text-2xl font-bold mb-6">Thêm danh mục mới</h1>

    <?php if ($error): ?>
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?= e($error) ?></div>
    <?php endif; ?>

    <form action="" method="POST" class="<?= UI_CARD ?> p-6 bg-white shadow rounded-lg">
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Tên danh mục</label>
            <input type="text" name="name" class="<?= UI_INPUT ?> w-full border p-2 rounded" required>
        </div>
        <div class="flex gap-4">
            <button type="submit" class="<?= ui_btn('primary') ?>">Lưu danh mục</button>
            <a href="index.php" class="<?= ui_btn('secondary') ?>">Hủy</a>
        </div>
    </form>
</div>

require_once __DIR__ . '/../../includes/admin_footer.php';