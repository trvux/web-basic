<?php
// Chi tiết sản phẩm, BẢN CƠ BẢN do leader làm để trang chủ có chỗ bấm vào.
// Minh (việc S2) làm tiếp: bảng thông số từ product_specs, bài viết, sản phẩm cùng hãng.
require_once __DIR__ . '/../includes/header.php';

$stmt = $pdo->prepare(
    'SELECT p.*, c.name AS category_name
     FROM products p LEFT JOIN categories c ON p.category_id = c.id
     WHERE p.id = ?'
);
$stmt->execute([(int)($_GET['id'] ?? 0)]);
$p = $stmt->fetch();
if (!$p) http_response_code(404);
?>

<?php if (!$p): ?>
  <div class="<?= UI_EMPTY ?>">
    <div class="<?= UI_EMPTY_TITLE ?>">Không tìm thấy sản phẩm</div>
    <a href="/index.php" class="<?= ui_btn('outline') ?> mt-3">Về trang chủ</a>
  </div>
<?php else: ?>
  <?= ui_breadcrumb(['Trang chủ' => '/index.php', $p['name'] => null]) ?>
  <div class="grid items-start gap-6 md:grid-cols-2">
    <img src="<?= e($p['image']) ?>" alt="<?= e($p['name']) ?>" class="aspect-square w-full rounded-xl bg-card object-cover ring-1 ring-foreground/10">
    <div class="grid gap-3">
      <?php if ($p['category_name']): ?><span class="<?= ui_badge('secondary') ?>"><?= e($p['category_name']) ?></span><?php endif; ?>
      <h1 class="text-2xl font-semibold tracking-tight"><?= e($p['name']) ?></h1>
      <div class="flex flex-wrap items-baseline gap-3">
        <span class="text-3xl font-bold text-primary"><?= number_format($p['price']) ?>đ</span>
        <?php if (!empty($p['old_price']) && $p['old_price'] > $p['price']): ?>
          <span class="text-muted-foreground line-through"><?= number_format($p['old_price']) ?>đ</span>
        <?php endif; ?>
      </div>
      <p class="text-muted-foreground"><?= e($p['description']) ?></p>
      <p class="text-sm"><?= $p['stock'] > 0 ? 'Còn ' . (int)$p['stock'] . ' sản phẩm' : 'Tạm hết hàng' ?></p>
      <?php if ($p['stock'] > 0): ?>
        <a href="/cart/add.php?id=<?= $p['id'] ?>" class="<?= ui_btn('default', 'lg') ?> w-fit">Thêm vào giỏ</a>
      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
