<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/admin_header.php';
$error = '';
$id = $_GET['id'] ?? null;

// Nếu không có ID trên URL thì đá về trang danh sách
if (!$id) {
    header("Location: index.php");
    exit;
}

// Lấy thông tin danh mục hiện tại
$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->execute([$id]);
$category = $stmt->fetch();

if (!$category) {
    die('Danh mục không tồn tại.');
}

// Nếu bấm nút Cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    
    if (empty($name)) {
        $error = 'Vui lòng nhập tên danh mục.';
    } else {
        $updateStmt = $pdo->prepare("UPDATE categories SET name = ? WHERE id = ?");
        if ($updateStmt->execute([$name, $id])) {
            header("Location: index.php");
            exit;
        } else {
            $error = 'Có lỗi xảy ra khi cập nhật.';
        }
    }
}
?>

<div class="p-6 max-w-lg mx-auto">
    <h1 class="text-2xl font-bold mb-6">Sửa danh mục</h1>

    <?php if ($error): ?>
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?= e($error) ?></div>
    <?php endif; ?>

    <form action="" method="POST" class="<?= UI_CARD ?> p-6 bg-white shadow rounded-lg">
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Tên danh mục</label>
            <!-- Dùng e() để in dữ liệu cũ ra, tránh lỗi XSS -->
            <input type="text" name="name" value="<?= e($category['name']) ?>" class="<?= UI_INPUT ?> w-full border p-2 rounded" required>
        </div>
        <div class="flex gap-4">
            <button type="submit" class="<?= ui_btn('primary') ?>">Cập nhật</button>
            <a href="index.php" class="<?= ui_btn('secondary') ?>">Hủy</a>
        </div>
    </form>
</div>

require_once __DIR__ . '/../../includes/admin_footer.php';