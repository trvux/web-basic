<?php
require_once __DIR__ . '/../../includes/admin_header.php';
require_once __DIR__ . '/../../includes/order_status.php';

$sql = "SELECT o.*, u.username 
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id 
        ORDER BY o.id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$orders = $stmt->fetchAll();
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="<?= UI_H1 ?>">Quản lý đơn hàng</h1>
</div>

<div class="<?= UI_TABLE_WRAP ?>">
    <table class="<?= UI_TABLE ?>">
        <thead>
            <tr class="<?= UI_TR ?>">
                <th class="<?= UI_TH ?>">Mã ĐH</th>
                <th class="<?= UI_TH ?>">Khách hàng</th>
                <th class="<?= UI_TH ?>">Ngày đặt</th>
                <th class="<?= UI_TH ?>">Tổng tiền</th>
                <th class="<?= UI_TH ?>">Trạng thái</th>
                <th class="<?= UI_TH ?>">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $row): ?>
            <tr class="<?= UI_TR ?>">
                <td class="<?= UI_TD ?>">#<?= e($row['id']) ?></td>
                <td class="<?= UI_TD ?>"><?= e($row['username'] ?? 'Khách vãng lai') ?></td>
                <td class="<?= UI_TD ?>"><?= e($row['created_at'] ?? '') ?></td>
                <td class="<?= UI_TD ?>"><?= number_format($row['total'] ?? 0, 0, ',', '.') ?> đ</td>
                <td class="<?= UI_TD ?>">
                    <?= order_status_badge($row['status']) ?>
                </td>
                <td class="<?= UI_TD ?>">
                    <a href="detail.php?id=<?= e($row['id']) ?>" class="<?= ui_btn('outline', 'sm') ?>">Xem chi tiết</a>
                </td>
            </tr>
            <?php endforeach; ?>
            
            <?php if (empty($orders)): ?>
            <tr>
                <td colspan="6" class="<?= UI_TD ?> text-center">
                    <div class="<?= UI_EMPTY ?>">Chưa có đơn hàng nào.</div>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>