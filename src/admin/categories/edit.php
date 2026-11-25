<?php
// Lỗi 5: Đã bỏ require_once db.php
require_once __DIR__ . '/../../includes/admin_header.php'; 

$error = '';
// Lỗi 4: Ép kiểu (int) cho ID lấy từ URL
$id = (int)($_GET['id'] ?? 0);

if (!$id) {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->execute([$id]);
$category = $stmt->fetch();

if (!$category) {
    die('Danh mục không tồn tại.');
}

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
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Sửa danh mục</h1>

    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?= e($error) ?></div>
    <?php endif; ?>

    <!-- Lỗi 2: Trả lại helper UI_CARD -->
    <form action="" method="POST" class="<?= UI_CARD ?> p-6">
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Tên danh mục</label>
            <!-- Lỗi 2: Trả lại helper UI_INPUT -->
            <input type="text" name="name" value="<?= e($category['name']) ?>" class="<?= UI_INPUT ?> w-full" required>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="<?= ui_btn('primary') ?>">Cập nhật</button>
            <a href="index.php" class="<?= ui_btn('secondary') ?>">Hủy</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>