<?php
require_once __DIR__ . '/../../includes/admin_header.php';

$stmt = $pdo->prepare("SELECT * FROM users ORDER BY id DESC");
$stmt->execute();
$users = $stmt->fetchAll();
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="<?= UI_H1 ?>">Quản lý người dùng</h1>
    <a href="create.php" class="<?= ui_btn() ?>">Thêm người dùng</a>
</div>

<div class="<?= UI_TABLE_WRAP ?>">
    <table class="<?= UI_TABLE ?>">
        <thead>
            <tr class="<?= UI_TR ?>">
                <th class="<?= UI_TH ?>">ID</th>
                <th class="<?= UI_TH ?>">Tên</th>
                <th class="<?= UI_TH ?>">Email</th>
                <th class="<?= UI_TH ?>">Vai trò</th>
                <th class="<?= UI_TH ?>">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $row): ?>
            <tr class="<?= UI_TR ?>">
                <td class="<?= UI_TD ?>"><?= e($row['id']) ?></td>
                <td class="<?= UI_TD ?>"><?= e($row['name']) ?></td>
                <td class="<?= UI_TD ?>"><?= e($row['email']) ?></td>
                <td class="<?= UI_TD ?>">
                    <?php if ($row['role'] === 'admin'): ?>
                        <span class="<?= ui_badge('default') ?>">Admin</span>
                    <?php else: ?>
                        <span class="<?= ui_badge('secondary') ?>">User</span>
                    <?php endif; ?>
                </td>
                <td class="<?= UI_TD ?>">
                    <a href="edit.php?id=<?= e($row['id']) ?>" class="<?= ui_btn('outline', 'sm') ?> mr-2">Sửa</a>
                    <form action="delete.php" method="POST" class="inline-block" onsubmit="return confirm('Xóa người dùng này?');">
                        <input type="hidden" name="id" value="<?= e($row['id']) ?>">
                        <button type="submit" class="<?= ui_btn('destructive', 'sm') ?>">Xóa</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            
            <?php if (empty($users)): ?>
            <tr>
                <td colspan="5" class="<?= UI_TD ?> text-center">
                    <div class="<?= UI_EMPTY ?>">Chưa có người dùng nào.</div>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>