<?php
require_once __DIR__ . '/../../includes/admin_header.php';

$stmt = $pdo->prepare("SELECT * FROM users ORDER BY id DESC");
$stmt->execute();
$users = $stmt->fetchAll();
?>

<div class="mb-5 flex items-center justify-between">
    <h1 class="text-2xl font-semibold tracking-tight">Quản lý người dùng</h1>
    <a href="create.php" class="<?= ui_btn() ?>">Thêm người dùng</a>
</div>

<div class="<?= UI_TABLE_WRAP ?>">
    <table class="<?= UI_TABLE ?>">
        <thead>
            <tr class="<?= UI_TR ?>">
                <th class="<?= UI_TH ?>">ID</th>
                <th class="<?= UI_TH ?>">Tên đăng nhập</th>
                <th class="<?= UI_TH ?>">Họ tên</th>
                <th class="<?= UI_TH ?>">Vai trò</th>
                <th class="<?= UI_TH ?> text-right">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $row): ?>
            <tr class="<?= UI_TR ?>">
                <td class="<?= UI_TD ?>"><?= e($row['id']) ?></td>
                <td class="<?= UI_TD ?>"><?= e($row['username']) ?></td>
                <td class="<?= UI_TD ?>"><?= e($row['full_name'] ?? '') ?></td>
                <td class="<?= UI_TD ?>">
                    <?php if ($row['role'] === 'admin'): ?>
                        <span class="<?= ui_badge('default') ?>">Admin</span>
                    <?php else: ?>
                        <span class="<?= ui_badge('secondary') ?>">User</span>
                    <?php endif; ?>
                </td>
                <td class="<?= UI_TD ?> text-right whitespace-nowrap">
                    <a href="edit.php?id=<?= e($row['id']) ?>" class="<?= ui_btn('outline', 'sm') ?>">Sửa</a>
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