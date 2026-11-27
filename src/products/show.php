<?php
/**
 * S2 — Chi tiết sản phẩm
 * URL: /products/show.php?id=<id>
 *
 * Bao gồm:
 *  - Ảnh, tên, giá, giá gốc, mô tả
 *  - Bảng thông số kỹ thuật (product_specs, nhóm theo group_name)
 *  - Sản phẩm cùng hãng (tối đa 4, loại chính nó)
 *  - Bài viết liên quan (ưu tiên bài gắn product_id, fallback 3 bài mới)
 *  - Xử lý 404 khi id sai / không tồn tại
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/ui.php';

// ============ 1. LẤY & VALIDATE ID ============
$id = (int)($_GET['id'] ?? 0);

$product       = null;
$spec_groups   = [];   // ['CPU' => [row, row...], 'RAM' => [...], ...]
$same_brand    = [];

// ============ 2. TRUY VẤN DỮ LIỆU ============
if ($id > 0) {
    // --- 2.1. Sản phẩm chính ---
    $stmt = $pdo->prepare(
        'SELECT p.*, c.name AS category_name
         FROM products p
         LEFT JOIN categories c ON p.category_id = c.id
         WHERE p.id = ?
         LIMIT 1'
    );
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if ($product) {
        // --- 2.2. Thông số kỹ thuật (nhóm theo group_name) ---
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

        // --- 2.3. Sản phẩm cùng hãng (tối đa 4, loại chính nó) ---
        if (!empty($product['brand'])) {
            $stmt = $pdo->prepare(
                'SELECT id, name, image, price, old_price, stock
                 FROM products
                 WHERE brand = ? AND id <> ?
                 ORDER BY RAND()
                 LIMIT 4'
            );
            $stmt->execute([$product['brand'], $id]);
            $same_brand = $stmt->fetchAll();
        }
    } else {
        http_response_code(404);
    }
} else {
    http_response_code(404);
}

// ============ 3. HELPER ============
/** Format giá VNĐ: 25990000 → "25.990.000đ" */
function format_price($n): string
{
    return number_format((int)$n) . 'đ';
}

/** Ảnh fallback khi sản phẩm không có ảnh */
function product_image(?string $path): string
{
    $path = trim((string)$path);
    if ($path === '') {
        return '/assets/no-image.svg';
    }
    // Nếu là URL tuyệt đối (http/https) → giữ nguyên
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }
    // Nếu là đường dẫn tương đối không bắt đầu bằng / → thêm /uploads/products/
    if ($path[0] !== '/') {
        return '/uploads/products/' . $path;
    }
    return $path;
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container mx-auto px-4 py-6">

  <?php if (!$product): ?>

    <!-- ============ 404 STATE ============ -->
    <div class="<?= UI_ALERT_ERROR ?> text-center py-10">
      <p class="text-lg font-semibold mb-2">Không tìm thấy sản phẩm</p>
      <p class="text-sm text-muted-foreground mb-4">
        Sản phẩm có thể đã bị xóa hoặc đường dẫn không đúng.
      </p>
      <a href="index.php" class="<?= ui_btn() ?> inline-block">← Quay lại danh sách</a>
    </div>

  <?php else: ?>

    <!-- ============ BREADCRUMB ============ -->
    <nav class="text-sm text-muted-foreground mb-4 flex flex-wrap gap-1">
      <a href="/" class="hover:text-primary">Trang chủ</a>
      <span>/</span>
      <a href="index.php" class="hover:text-primary">Sản phẩm</a>
      <?php if (!empty($product['category_name'])): ?>
        <span>/</span>
        <a href="index.php?category_id=<?= (int)$product['category_id'] ?>"
           class="hover:text-primary"><?= e($product['category_name']) ?></a>
      <?php endif; ?>
      <span>/</span>
      <span class="text-foreground font-medium"><?= e($product['name']) ?></span>
    </nav>

    <!-- ============ KHỐI CHÍNH: ẢNH + INFO ============ -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-10">

      <!-- ẢNH -->
      <div class="<?= UI_CARD ?> overflow-hidden">
        <img src="<?= e(product_image($product['image'] ?? '')) ?>"
             alt="<?= e($product['name']) ?>"
             class="w-full h-auto object-contain p-4 max-h-[480px] mx-auto">
      </div>

      <!-- INFO -->
      <div class="flex flex-col">
        <h1 class="text-2xl lg:text-3xl font-bold leading-tight">
          <?= e($product['name']) ?>
        </h1>

        <!-- Meta: hãng + danh mục + tồn kho -->
        <div class="mt-2 flex flex-wrap items-center gap-2 text-sm">
          <?php if (!empty($product['brand'])): ?>
            <span class="<?= ui_badge() ?>"><?= e($product['brand']) ?></span>
          <?php endif; ?>
          <?php if (!empty($product['category_name'])): ?>
            <span class="text-muted-foreground"><?= e($product['category_name']) ?></span>
          <?php endif; ?>
          <?php if ((int)$product['stock'] > 0): ?>
            <span class="text-primary font-medium">
              Còn hàng (<?= (int)$product['stock'] ?>)
            </span>
          <?php else: ?>
            <span class="text-destructive font-medium">Hết hàng</span>
          <?php endif; ?>
        </div>

        <!-- Giá -->
        <div class="mt-4 flex items-baseline gap-3 flex-wrap">
          <span class="text-3xl font-bold text-primary">
            <?= format_price($product['price']) ?>
          </span>
          <?php if (!empty($product['old_price']) && (int)$product['old_price'] > (int)$product['price']): ?>
            <span class="text-lg text-muted-foreground line-through">
              <?= format_price($product['old_price']) ?>
            </span>
            <?php
              $discount = 0;
              if ((int)$product['old_price'] > 0) {
                  $discount = round((1 - (int)$product['price'] / (int)$product['old_price']) * 100);
              }
            ?>
            <?php if ($discount > 0): ?>
              <span class="<?= ui_badge('destructive') ?>">
                -<?= $discount ?>%
              </span>
            <?php endif; ?>
          <?php endif; ?>
        </div>

        <!-- Nút hành động -->
        <div class="mt-6 flex flex-wrap gap-3">
          <?php if ((int)$product['stock'] > 0): ?>
             <form action="/cart/add.php" method="get" class="flex gap-2">
              <input type="hidden" name="id" value="<?= (int)$product['id'] ?>">
              <input type="number" name="qty" value="1" min="1" max="<?= (int)$product['stock'] ?>" aria-label="Số lượng" class="<?= UI_INPUT ?> w-20!">
              <button type="submit" class="<?= ui_btn() ?>">Thêm vào giỏ</button>
            </form>
          <?php else: ?>
            <span class="<?= ui_btn() ?> opacity-50 pointer-events-none cursor-not-allowed">
              Hết hàng
            </span>
          <?php endif; ?>
          <a href="index.php" class="<?= ui_btn('outline') ?>">← Quay lại</a>
        </div>

        <!-- Mô tả ngắn -->
        <?php if (!empty($product['description'])): ?>
          <div class="mt-6 pt-6 border-t">
            <h2 class="text-sm font-semibold mb-2 text-muted-foreground uppercase tracking-wide">
              Mô tả
            </h2>
            <div class="text-sm leading-relaxed">
              <?= nl2br(e($product['description'])) ?>
            </div>
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
                <!-- Dòng tiêu đề nhóm -->
                <tr class="bg-muted/50">
                  <th colspan="2" class="px-4 py-2 text-left font-semibold text-sm">
                    <?= e($group) ?>
                  </th>
                </tr>
                <!-- Các dòng thông số trong nhóm -->
                <?php foreach ($items as $item): ?>
                  <tr class="<?= UI_TR ?>">
                    <td class="px-4 py-2 text-sm text-muted-foreground w-1/3 align-top">
                      <?= e($item['label']) ?>
                    </td>
                    <td class="px-4 py-2 text-sm">
                      <?= e($item['value']) ?>
                    </td>
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
        <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
          <h2 class="text-xl font-bold">
            Sản phẩm cùng hãng <?= e($product['brand']) ?>
          </h2>
          <a href="index.php?brand=<?= urlencode($product['brand']) ?>"
             class="text-primary text-sm font-medium hover:underline">
            Xem tất cả →
          </a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
          <?php foreach ($same_brand as $sp): ?>
            <a href="show.php?id=<?= (int)$sp['id'] ?>"
               class="<?= UI_CARD ?> hover:shadow-md transition flex flex-col">
              <img src="<?= e(product_image($sp['image'] ?? '')) ?>"
                   alt="<?= e($sp['name']) ?>"
                   class="h-32 md:h-40 w-full object-cover bg-muted">
              <div class="px-3 py-2 flex flex-col flex-1">
                <h3 class="text-sm font-semibold line-clamp-2 min-h-[2.5rem]">
                  <?= e($sp['name']) ?>
                </h3>
                <div class="mt-auto pt-1">
                  <span class="text-primary font-bold text-sm">
                    <?= format_price($sp['price']) ?>
                  </span>
                  <?php if (!empty($sp['old_price'])): ?>
                    <span class="text-xs text-muted-foreground line-through block">
                      <?= format_price($sp['old_price']) ?>
                    </span>
                  <?php endif; ?>
                </div>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endif; ?>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
