<?php
// Kết nối Database (đường dẫn lùi 2 cấp để tới thư mục src/config)
require_once __DIR__ . '/../../config/db.php';
// Gọi header của admin
require_once __DIR__ . '/../../includes/admin_header.php';
// Lấy danh sách danh mục
$stmt = $pdo->prepare("SELECT * FROM categories ORDER BY id DESC");
$stmt->execute();
$categories = $stmt->fetchAll();
?>

<div class="p-6 max-w-5xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Quản lý danh mục</h1>
        <!-- Nút thêm mới sử dụng hàm ui_btn -->
        <a href="create.php" class="<?= ui_btn('primary') ?>">Thêm danh mục mới</a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden <?= UI_CARD ?>">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tên danh mục</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hành động</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($categories as $row): ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap"><?= e($row['id']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                        <?= e($row['name']) ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="edit.php?id=<?= e($row['id']) ?>" class="text-blue-600 hover:text-blue-900 mr-4">Sửa</a>
                        
                        <!-- Xóa dùng POST để bảo mật, tránh bị xóa nhầm qua link -->
                        <form action="delete.php" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này?');">
                            <input type="hidden" name="id" value="<?= e($row['id']) ?>">
                            <button type="submit" class="text-red-600 hover:text-red-900">Xóa</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if (empty($categories)): ?>
                <tr>
                    <td colspan="3" class="px-6 py-4 text-center text-gray-500">Chưa có danh mục nào.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

require_once __DIR__ . '/../../includes/admin_footer.php';