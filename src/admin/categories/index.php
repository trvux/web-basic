<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/admin_header.php'; 

$stmt = $pdo->prepare("SELECT * FROM categories ORDER BY id DESC");
$stmt->execute();
$categories = $stmt->fetchAll();
?>

<div class="p-6 max-w-5xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Quản lý danh mục</h1>
        <a href="create.php" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">Thêm danh mục mới</a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên danh mục</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hành động</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($categories as $row): ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= e($row['id']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= e($row['name']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="edit.php?id=<?= e($row['id']) ?>" class="text-blue-600 hover:text-blue-900 mr-4">Sửa</a>
                        <form action="delete.php" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này?');">
                            <input type="hidden" name="id" value="<?= e($row['id']) ?>">
                            <button type="submit" class="text-red-600 hover:text-red-900">Xóa</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if (empty($categories)): ?>
                <tr>
                    <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">Chưa có danh mục nào.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/admin_footer.php'; ?>