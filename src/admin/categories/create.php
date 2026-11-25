<?php
require_once __DIR__ . '/../../config/db.php';
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

    <form action="" method="POST" class="bg-white shadow rounded-lg border border-gray-200 p-6">
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Tên danh mục</label>
            <input type="text" name="name" class="w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-red-500 focus:border-red-500 outline-none" required>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">Lưu danh mục</button>
            <a href="index.php" class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300 transition">Hủy</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/admin_footer.php'; ?>