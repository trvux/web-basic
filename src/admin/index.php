<?php
require_once __DIR__ . '/../includes/admin_header.php';

$totalProducts = $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$totalCategories = $pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn();
$totalOrders = $pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn();
$revenue = $pdo->query('SELECT COALESCE(SUM(total), 0) FROM orders')->fetchColumn();

$stats = [
    ['Sản phẩm', $totalProducts, '/admin/products/index.php'],
    ['Danh mục', $totalCategories, '/admin/categories/index.php'],
    ['Đơn hàng', $totalOrders, '/admin/orders/index.php'],
    ['Doanh thu', number_format($revenue) . 'đ', '/admin/orders/index.php'],
];
?>

<h1 class="<?= UI_H1 ?>">Dashboard</h1>

<div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
  <?php foreach ($stats as [$label, $value, $link]): ?>
    <a href="<?= $link ?>" class="<?= UI_CARD ?> transition-colors hover:bg-muted/50">
      <div class="<?= UI_CARD_HEADER ?>">
        <p class="<?= UI_CARD_DESC ?>"><?= $label ?></p>
        <p class="text-2xl font-semibold text-primary"><?= $value ?></p>
      </div>
    </a>
  <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
