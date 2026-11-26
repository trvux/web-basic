<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/ui.php';

$id = (int)($_GET['id'] ?? 0);
$product       = null;
$spec_groups   = [];
$same_brand    = [];
$related_posts = [];

if ($id > 0) {
    // --- 1. Lấy sản phẩm ---
    $stmt = $pdo->prepare(
        'SELECT p.*, c.name AS category_name
         FROM products p
         LEFT JOIN categories c ON p.category_id = c.id
         WHERE p.id = ?'
    );
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if ($product) {
        // --- 2. Thông số kỹ thuật (nhóm theo group_name) ---
        $stmt = $pdo->prepare(
            'SELECT group_name, label, value
             FROM product_specs
             WHERE product_id = ?
             ORDER BY sort_order, id'
        );
        $stmt->execute([$id]);
        foreach ($stmt->fetchAll() as $s) {
            $group = $s['group_name'] ?: 'Thông số chung';
            $spec_groups[$group][] = $s;
        }

        // --- 3. Sản phẩm cùng hãng (tối đa 4) ---
        if (!empty($product['brand'])) {
            $stmt = $pdo->prepare(
                'SELECT id, name, image, price, old_price
                 FROM products
                 WHERE brand = ? AND id <> ?
                 ORDER BY RAND()
                 LIMIT 4'
            );
            $stmt->execute([$product['brand'], $id]);
            $same_brand = $stmt->fetchAll();
        }

        // --- 4. Bài viết liên quan ---
        // Ưu tiên bài có product_id = sản phẩm này.
        // Nếu bảng posts không có cột product_id → bỏ try đầu, dùng fallback.
        try {
            $stmt = $pdo->prepare(
                'SELECT id, title, slug, image, excerpt, created_at
                 FROM posts
                 WHERE product_id = ?
                 ORDER BY created_at DESC
                 LIMIT 3'
            );
            $stmt->execute([$id]);
            $related_posts = $stmt->fetchAll();
        } catch (PDOException $e) {
            $related_posts = []; // bảng chưa có product_id → bỏ qua
        }

        // Fallback: 3 bài mới nhất nếu không có bài gắn sản phẩm
        if (!$related_posts) {
            $related_posts = $pdo->query(
                'SELECT id, title, slug, image, excerpt, created_at
                 FROM posts
                 ORDER BY created_at DESC
                 LIMIT 3'
            )->fetchAll();
        }
    } else {
        http_response_code(404);
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container mx-auto px-4 py-6">
  <?php if (!$product): ?>
    <div class="<?= UI_ALERT_ERROR ?>">
      Không tìm thấy sản phẩm.
      <a href="index.php" class="underline">Quay lại danh sách</a>
    </div>
  <?php else: ?>

    <!-- ============ BREADCRUMB ============ -->
    <nav class="text-sm text-muted-foreground mb-4">
      <a href="/">Trang chủ</a> /
      <a href="index.php">Sản phẩm</a> /
      <span><?= e($product['name']) ?></span>
    </nav>

    <!-- ============ PHẦN CHÍNH: ẢNH + INFO ============ -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <div class="<?= UI_CARD ?>">
        <img src="<?= e($product['image'] ?: '/uploads/products/placeholder.jpg') ?>"
             alt="<?= e($product['name']) ?>"
             class="w-full h-auto object-contain p-4">
      </div>

      <div>
        <h1 class="text-2xl font-bold"><?= e($product['name']) ?></h1>
        <p class="text-muted-foreground mt-1">
          <?= e($product['category_name'] ?? '') ?>
          <?php if (!empty($product['brand'])): ?>
            · Hãng <strong><?= e($product['brand']) ?></strong>
          <?php endif; ?>
        </p>

        <div class="mt-4">
          <span class="text-3xl font-bold text-primary"><?= number_format($product['price']) ?>đ</span>
          <?php if (!empty($product['old_price'])): ?>
            <span class="text-lg text-muted-foreground line-through ml-2">
              <?= number_format($product['old_price']) ?>đ
            </span>
          <?php endif; ?>
        </div>

        <p class="mt-2 text-sm <?= $product['stock'] > 0 ? 'text-green-600' : 'text-red-500' ?>">
          <?= $product['stock'] > 0
              ? 'Còn hàng (' . (int)$product['stock'] . ')'
              : 'Hết hàng' ?>
        </p>

        <div class="mt-6 flex gap-3 flex-wrap">
          <a href="/cart/add.php?id=<?= (int)$product['id'] ?>"
             class="<?= ui_btn() ?><?= $product['stock'] <= 0 ? ' pointer-events-none opacity-50' : '' ?>">
            Thêm vào giỏ
          </a>
          <a href="index.php" class="<?= ui_btn('outline') ?>">Quay lại</a>
        </div>

        <?php if ($product['description']): ?>
          <div class="mt-6 prose max-w-none text-sm">
            <?= nl2br(e($product['description'])) ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- ============ BẢNG THÔNG SỐ KỸ THUẬT ============ -->
    <?php if ($spec_groups): ?>
      <section class="mt-10">
        <h2 class="text-xl font-bold mb-4">Thông số kỹ thuật</h2>
        <div class="<?= UI_TABLE_WRAP ?>">
          <table class="<?= UI_TABLE ?>">
            <tbody>
              <?php foreach ($spec_groups as $group => $items): ?>
                <tr class="bg-muted/50">
                  <th colspan="2" class="px-4 py-2 text-left font-semibold">
                    <?= e($group) ?>
                  </th>
                </tr>
                <?php foreach ($items as $item): ?>
                  <tr class="<?= UI_TR ?>">
                    <td class="px-4 py-2 text-muted-foreground w-1/3">
                      <?= e($item['label']) ?>
                    </td>
                    <td class="px-4 py-2"><?= e($item['value']) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </section>
    <?php endif; ?>

    <!-- ============ SẢN PHẨM CÙNG HÃNG ============ -->
    <?php if ($same_brand): ?>
      <section class="mt-10">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-xl font-bold">
            Sản phẩm cùng hãng <?= e($product['brand']) ?>
          </h2>
          <a href="index.php?brand=<?= urlencode($product['brand']) ?>"
             class="text-primary text-sm underline">
            Xem tất cả →
          </a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <?php foreach ($same_brand as $sp): ?>
            <a href="show.php?id=<?= (int)$sp['id'] ?>"
               class="<?= UI_CARD ?> hover:shadow-md transition">
              <img src="<?= e($sp['image'] ?: '/uploads/products/placeholder.jpg') ?>"
                   alt="<?= e($sp['name']) ?>"
                   class="h-32 w-full object-cover">
              <div class="px-3 py-2">
                <h3 class="text-sm font-semibold line-clamp-2">
                  <?= e($sp['name']) ?>
                </h3>
                <p class="text-primary font-bold mt-1 text-sm">
                  <?= number_format($sp['price']) ?>đ
                </p>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endif; ?>

    <!-- ============ BÀI VIẾT LIÊN QUAN ============ -->
    <?php if ($related_posts): ?>
      <section class="mt-10">
        <h2 class="text-xl font-bold mb-4">Bài viết liên quan</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <?php foreach ($related_posts as $post): ?>
            <a href="/posts/show.php?id=<?= (int)$post['id'] ?>"
               class="<?= UI_CARD ?> hover:shadow-md transition">
              <?php if (!empty($post['image'])): ?>
                <img src="<?= e($post['image']) ?>"
                     alt="<?= e($post['title']) ?>"
                     class="h-32 w-full object-cover">
              <?php endif; ?>
              <div class="px-3 py-3">
                <h3 class="text-sm font-semibold line-clamp-2">
                  <?= e($post['title']) ?>
                </h3>
                <?php if (!empty($post['excerpt'])): ?>
                  <p class="text-xs text-muted-foreground mt-1 line-clamp-2">
                    <?= e($post['excerpt']) ?>
                  </p>
                <?php endif; ?>
                <?php if (!empty($post['created_at'])): ?>
                  <p class="text-xs text-muted-foreground mt-2">
                    <?= date('d/m/Y', strtotime($post['created_at'])) ?>
                  </p>
                <?php endif; ?>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endif; ?>

  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
