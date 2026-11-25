<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/ui.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    http_response_code(404);
    $product = null;
} else {
    $stmt = $pdo->prepare('SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?');
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if (!$product) {
        http_response_code(404);
    } else {
        $stmt = $pdo->prepare('SELECT group_name, label, value FROM product_specs WHERE product_id = ? ORDER BY sort_order');
        $stmt->execute([$id]);
        $specs = $stmt->fetchAll();

        // Nhóm thông số theo group_name
        $spec_groups = [];
        foreach ($specs as $s) {
            $spec_groups[$s['group_name']][] = $s;
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container mx-auto px-4 py-6">
  <?php if (!$product): ?>
    <div class="<?= UI_ALERT_ERROR ?>">
      Không tìm thấy sản phẩm. <a href="index.php" class="underline">Quay lại danh sách</a>
    </div>
  <?php else: ?>
    <!-- Breadcrumb -->
    <nav class="text-sm text-muted-foreground mb-4">
      <a href="/">Trang chủ</a> / <a href="index.php">Sản phẩm</a> / <span><?= e($product['name']) ?></span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <!-- Ảnh -->
      <div class="<?= UI_CARD ?>">
        <img src="<?= e($product['image'] ?: '/uploads/products/placeholder.jpg') ?>"
             alt="<?= e($product['name']) ?>" class="w-full h-auto object-contain p-4">
      </div>

      <!-- Thông tin chính -->
      <div>
        <h1 class="text-2xl font-bold"><?= e($product['name']) ?></h1>
        <p class="text-muted-foreground mt-1"><?= e($product['category_name'] ?? '') ?></p>

        <div class="mt-4">
          <span class="text-3xl font-bold text-primary"><?= number_format($product['price']) ?>đ</span>
          <?php if (!empty($product['old_price'])): ?>
            <span class="text-lg text-muted-foreground line-through ml-2"><?= number_format($product['old_price']) ?>đ</span>
          <?php endif; ?>
        </div>

        <p class="mt-2 text-sm <?= $product['stock'] > 0 ? 'text-green-600' : 'text-red-500' ?>">
          <?= $product['stock'] > 0 ? 'Còn hàng (' . $product['stock'] . ')' : 'Hết hàng' ?>
        </p>

        <div class="mt-6 flex gap-3">
          <a href="/cart/add.php?id=<?= $product['id'] ?>" class="<?= ui_btn() ?>">Thêm vào giỏ</a>
          <a href="index.php" class="<?= ui_btn('outline') ?>">Quay lại</a>
        </div>

        <!-- Mô tả ngắn -->
        <?php if ($product['description']): ?>
          <div class="mt-6 prose max-w-none">
            <?= nl2br(e($product['description'])) ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Bảng thông số -->
    <?php if (!empty($spec_groups)): ?>
      <div class="mt-10">
        <h2 class="text-xl font-bold mb-4">Thông số kỹ thuật</h2>
        <div class="<?= UI_TABLE_WRAP ?>">
          <table class="<?= UI_TABLE ?>">
            <tbody>
              <?php foreach ($spec_groups as $group => $items): ?>
                <tr class="bg-muted/50">
                  <th colspan="2" class="px-4 py-2 text-left font-semibold"><?= e($group) ?></th>
                </tr>
                <?php foreach ($items as $item): ?>
                  <tr class="<?= UI_TR ?>">
                    <td class="px-4 py-2 text-muted-foreground w-1/3"><?= e($item['label']) ?></td>
                    <td class="px-4 py-2"><?= e($item['value']) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php endif; ?>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>