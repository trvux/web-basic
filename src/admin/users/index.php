<?php
// Lùi 1 cấp (../) vì file nằm ngay trong thư mục admin
require_once __DIR__ . '/../includes/admin_header.php';

$revStmt = $pdo->query("SELECT SUM(total) FROM orders WHERE status IN ('done', 'shipping')");
$total_revenue = $revStmt->fetchColumn() ?: 0;

$orderStmt = $pdo->query("SELECT COUNT(id) FROM orders");
$total_orders = $orderStmt->fetchColumn() ?: 0;

$prodStmt = $pdo->query("SELECT COUNT(id) FROM products");
$total_products = $prodStmt->fetchColumn() ?: 0;

$catStmt = $pdo->query("SELECT COUNT(id) FROM categories");
$total_categories = $catStmt->fetchColumn() ?: 0;

$userStmt = $pdo->query("SELECT COUNT(id) FROM users");
$total_users = $userStmt->fetchColumn() ?: 0;

$recentOrdersStmt = $pdo->query("SELECT o.*, u.username FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.id DESC LIMIT 5");
$recent_orders = $recentOrdersStmt->fetchAll();
?>

<div class="mb-6">
    <h1 class="<?= UI_H1 ?>">Dashboard</h1>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
    <div class="<?= UI_CARD ?> p-4 flex flex-col justify-between">
        <span class="text-muted-foreground text-sm font-medium">Sản phẩm</span>
        <span class="text-2xl font-bold mt-2 text-primary"><?= number_format($total_products) ?></span>
    </div>
    <div class="<?= UI_CARD ?> p-4 flex flex-col justify-between">
        <span class="text-muted-foreground text-sm font-medium">Danh mục</span>
        <span class="text-2xl font-bold mt-2 text-primary"><?= number_format($total_categories) ?></span>
    </div>
    <div class="<?= UI_CARD ?> p-4 flex flex-col justify-between">
        <span class="text-muted-foreground text-sm font-medium">Đơn hàng</span>
        <span class="text-2xl font-bold mt-2 text-primary"><?= number_format($total_orders) ?></span>
    </div>
    <div class="<?= UI_CARD ?> p-4 flex flex-col justify-between">
        <span class="text-muted-foreground text-sm font-medium">Người dùng</span>
        <span class="text-2xl font-bold mt-2 text-primary"><?= number_format($total_users) ?></span>
    </div>
    <div class="<?= UI_CARD ?> p-4 flex flex-col justify-between">
        <span class="text-muted-foreground text-sm font-medium">Doanh thu</span>
        <span class="text-2xl font-bold mt-2 text-primary"><?= number_format($total_revenue, 0, ',', '.') ?> đ</span>
    </div>
</div>

<div class="flex justify-between items-center mb-4 mt-8">
    <h2 class="text-xl font-bold">Đơn hàng mới nhất</h2>
    <a href="orders/index.php" class="<?= ui_btn('outline', 'sm') ?>">Xem tất cả</a>
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
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recent_orders as $row): ?>
            <tr class="<?= UI_TR ?>">
                <td class="<?= UI_TD ?>">#<?= e($row['id']) ?></td>
                <td class="<?= UI_TD ?>"><?= e($row['username'] ?? 'Khách vãng lai') ?></td>
                <td class="<?= UI_TD ?>"><?= e($row['created_at']) ?></td>
                <td class="<?= UI_TD ?>"><?= number_format($row['total'], 0, ',', '.') ?> đ</td>
                <td class="<?= UI_TD ?>">
                    <?php 
                        $status = $row['status'];
                        $badgeType = 'secondary';
                        $statusText = 'Mới';
                        if ($status === 'shipping') { $badgeType = 'outline'; $statusText = 'Đang giao'; } 
                        elseif ($status === 'done') { $badgeType = 'default'; $statusText = 'Hoàn thành'; } 
                        elseif ($status === 'cancelled') { $badgeType = 'destructive'; $statusText = 'Đã hủy'; }
                    ?>
                    <span class="<?= ui_badge($badgeType) ?>"><?= e($statusText) ?></span>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($recent_orders)): ?>
            <tr>
                <td colspan="5" class="<?= UI_TD ?> text-center py-4 text-muted-foreground">Chưa có đơn hàng nào.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php 
// Lùi 1 cấp (../)
require_once __DIR__ . '/../includes/footer.php'; 
?>