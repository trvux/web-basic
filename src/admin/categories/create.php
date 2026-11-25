<?php
// Lỗi 5: Đã bỏ require_once db.php vì admin_header đã lo
require_once __DIR__ . '/../../includes/admin_header.php'; 

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    
    if (empty($name)) {
        $error = 'Vui lòng nhập tên danh mục.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
        if ($stmt->execute([$name])) {
            header("Location: index.php");
            exit;
        } else {
            $error = 'Có lỗi xảy ra khi thêm danh mục.';
        }
    }
}
?>

<div class="p-6 max-w-lg mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Thêm danh mục mới</h1>

    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?= e($error) ?></div>
    <?php endif; ?>

    <!-- Lỗi 2: Trả lại helper UI_CARD -->
    <form action="" method="POST" class="<?= UI_CARD ?> p-6">
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Tên danh mục</label>
            <!-- Lỗi 2: Trả lại helper UI_INPUT -->
            <input type="text" name="name" class="<?= UI_INPUT ?> w-full" required>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="<?= ui_btn('primary') ?>">Lưu danh mục</button>
            <a href="index.php" class="<?= ui_btn('secondary') ?>">Hủy</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>