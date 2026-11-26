<?php
require_once __DIR__ . '/../includes/admin_header.php';
require_once __DIR__ . '/../includes/order_status.php';

// 1. Lấy các số liệu thống kê
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

// Số đơn theo từng trạng thái: ['new' => 2, 'done' => 1, ...]
$by_status = $pdo->query("SELECT status, COUNT(*) FROM orders GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);

// Thẻ thống kê: [nhãn, giá trị, trang mở khi bấm]
$stats = [
    ['Sản phẩm', number_format($total_products), '/admin/products/index.php'],
    ['Danh mục', number_format($total_categories), '/admin/categories/index.php'],
    ['Đơn hàng', number_format($total_orders), '/admin/orders/index.php'],
    ['Người dùng', number_format($total_users), '/admin/users/index.php'],
    ['Doanh thu', number_format($total_revenue, 0, ',', '.') . ' đ', '/admin/orders/index.php'],
];

// 2. Lấy 5 đơn hàng mới nhất
$recentOrdersStmt = $pdo->query("SELECT o.*, u.username FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.id DESC LIMIT 5");
$recent_orders = $recentOrdersStmt->fetchAll();
?>

<div class="mb-6">
    <h1 class="<?= UI_H1 ?>">Dashboard</h1>
</div>

<!-- Khu vực 5 thẻ thống kê (bấm để mở trang tương ứng) -->
<div class="mb-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
    <?php foreach ($stats as [$label, $value, $link]): ?>
        <a href="<?= $link ?>" class="<?= UI_CARD ?> gap-2! p-4 transition-colors hover:bg-muted/50">
            <span class="text-sm font-medium text-muted-foreground"><?= $label ?></span>
            <span class="text-2xl font-bold whitespace-nowrap text-primary"><?= $value ?></span>
        </a>
    <?php endforeach; ?>
</div>
<p class="mb-6 text-xs text-muted-foreground">Doanh thu chỉ tính đơn đang giao và hoàn tất.</p>

<h2 class="mb-4 text-xl font-bold">Đơn theo trạng thái</h2>
<div class="mb-8 grid grid-cols-2 gap-4 lg:grid-cols-4">
    <?php foreach (array_keys(ORDER_STATUSES) as $key): ?>
        <div class="<?= UI_CARD ?> gap-2! p-4">
            <span><?= order_status_badge($key) ?></span>
            <span class="text-2xl font-bold"><?= number_format($by_status[$key] ?? 0) ?></span>
        </div>
    <?php endforeach; ?>
</div>

<!-- Khu vực Đơn hàng mới nhất -->
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
                <td class="<?= UI_TD ?>"><a href="/admin/orders/detail.php?id=<?= e($row['id']) ?>" class="font-medium text-primary hover:underline">#<?= e($row['id']) ?></a></td>
                <td class="<?= UI_TD ?>"><?= e($row['username'] ?? 'Khách vãng lai') ?></td>
                <td class="<?= UI_TD ?>"><?= e($row['created_at']) ?></td>
                <td class="<?= UI_TD ?>"><?= number_format($row['total'], 0, ',', '.') ?> đ</td>
                <td class="<?= UI_TD ?>">
                    <?= order_status_badge($row['status']) ?>
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

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
