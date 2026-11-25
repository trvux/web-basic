<?php
require_once __DIR__ . '/../../includes/admin_header.php'; 

// Lấy danh sách user
$stmt = $pdo->prepare("SELECT * FROM users ORDER BY id DESC");
$stmt->execute();
$users = $stmt->fetchAll();
?>

<div class="p-6 max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Quản lý người dùng</h1>
        <!-- Lỗi 3: Dùng ui_btn thay vì màu viết cứng -->
        <a href="create.php" class="<?= ui_btn('primary') ?>">Thêm người dùng</a>
    </div>

    <!-- Lỗi 3: Dùng UI_TABLE (nếu file ui.php của bạn dùng tên khác, hãy sửa lại cho khớp) -->
    <div class="bg-white rounded-lg shadow overflow-hidden border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên đăng nhập</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Họ tên</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vai trò</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hành động</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($users as $row): ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= e($row['id']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= e($row['username']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= e($row['full_name'] ?? 'Chưa cập nhật') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <!-- Lỗi 3: Dùng ui_badge để hiển thị role -->
                        <span class="<?= ui_badge($row['role'] === 'admin' ? 'danger' : 'success') ?>">
                            <?= e($row['role']) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="edit.php?id=<?= e($row['id']) ?>" class="text-blue-600 hover:text-blue-900 mr-4">Sửa</a>
                        
                        <!-- Lỗi 1: Chặn tự xoá chính mình -->
                        <?php 
                        $current_user_id = (int)($_SESSION['user']['id'] ?? 0);
                        if ($row['id'] !== $current_user_id): 
                        ?>
                            <form action="delete.php" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa người dùng này?');">
                                <input type="hidden" name="id" value="<?= e($row['id']) ?>">
                                <button type="submit" class="text-red-600 hover:text-red-900">Xóa</button>
                            </form>
                        <?php else: ?>
                            <span class="text-gray-400 italic">(Chính bạn)</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <!-- Lỗi 5: Dùng UI_EMPTY thay vì bảng trống -->
                <?php if (empty($users)): ?>
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center">
                        <div class="<?= UI_EMPTY ?>">Chưa có người dùng nào.</div>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>