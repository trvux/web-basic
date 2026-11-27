<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/ui.php';

// ============ 1. ĐỌC THAM SỐ LỌC ============
$get = fn($k) => is_string($_GET[$k] ?? null) ? $_GET[$k] : '';
$q           = trim($get('q'));
$category_id = (int)($_GET['category_id'] ?? 0);
$ram         = (int)($_GET['ram'] ?? 0);
$ssd         = (int)($_GET['ssd'] ?? 0);
$price_min   = (int)($_GET['price_min'] ?? 0);
$price_max   = (int)($_GET['price_max'] ?? 0);
$sort        = $get('sort') ?: 'newest';
$page        = max(1, (int)($_GET['page'] ?? 1));
$per_page    = 12;


// ============ 2. XÂY WHERE ĐỘNG ============
$where  = [];
$params = [];

if ($q !== '') {
    // Escape ký tự đại diện của LIKE để user không lách wildcard
    $like = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $q) . '%';
    $where[]  = 'p.name LIKE ?';
    $params[] = $like;
}
if ($brand !== '') {
    $where[]  = 'p.brand = ?';
    $params[] = $brand;
}
if ($category_id > 0) {
    $where[]  = 'p.category_id = ?';
    $params[] = $category_id;
}
if ($ram > 0) {
    $where[]  = 'p.ram_gb = ?';
    $params[] = $ram;
}
if ($ssd > 0) {
    $where[]  = 'p.ssd_gb = ?';
    $params[] = $ssd;
}
if ($price_min > 0) {
    $where[]  = 'p.price >= ?';
    $params[] = $price_min;
}
if ($price_max > 0) {
    $where[]  = 'p.price <= ?';
    $params[] = $price_max;
}

$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// ============ 3. SẮP XẾP (whitelist) ============
$sort_map = [
    'newest'     => 'p.id DESC',
    'price_asc'  => 'p.price ASC',
    'price_desc' => 'p.price DESC',
    'name_asc'   => 'p.name ASC',
];
$order_sql = $sort_map[$sort] ?? $sort_map['newest'];

// ============ 4. ĐẾM TỔNG & TÍNH PHÂN TRANG ============
$stmt = $pdo->prepare("SELECT COUNT(*) FROM products p $where_sql");
$stmt->execute($params);
$total = (int)$stmt->fetchColumn();

$total_pages = max(1, (int)ceil($total / $per_page));
if ($page > $total_pages) $page = $total_pages;
$offset      = ($page - 1) * $per_page;
// ============ 5. LẤY DỮ LIỆU TRANG HIỆN TẠI ============
$sql = "SELECT p.id, p.name, p.image, p.price, p.old_price, p.brand,
               p.ram_gb, p.ssd_gb, p.stock,
               c.name AS category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        $where_sql
        ORDER BY $order_sql
        LIMIT $per_page OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// ============ 6. DỮ LIỆU CHO BỘ LỌC ============
$brands = $pdo->query(
    "SELECT DISTINCT brand FROM products
     WHERE brand IS NOT NULL AND brand <> ''
     ORDER BY brand"
)->fetchAll(PDO::FETCH_COLUMN);

$categories = $pdo->query(
    "SELECT id, name FROM categories ORDER BY name"
)->fetchAll();

// ============ 7. TIỆN ÍCH ============
// Giữ toàn bộ tham số GET khi chuyển trang
$page_url = function ($target) {
    $p = $_GET;
    $p['page'] = $target;
    return '?' . http_build_query($p);
};

// Đếm số filter đang bật
$active_filters = 0;
foreach (['q','brand','category_id','ram','ssd','price_min','price_max'] as $k) {
    if (!empty($_GET[$k])) $active_filters++;
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container mx-auto px-4 py-6">
  <!-- ============ HEADER ============ -->
  <div class="flex items-end justify-between mb-4 flex-wrap gap-2">
    <div>
      <h1 class="text-2xl font-bold">Sản phẩm</h1>
      <p class="text-sm text-muted-foreground">
        <?php if ($q !== '' || $active_filters > 0): ?>
          Tìm thấy <strong><?= $total ?></strong> sản phẩm phù hợp
        <?php else: ?>
          Có <strong><?= $total ?></strong> sản phẩm
        <?php endif; ?>
      </p>
    </div>
    <?php if ($active_filters > 0): ?>
      <a href="index.php" class="<?= ui_btn('outline') ?> text-sm">
        Xóa bộ lọc (<?= $active_filters ?>)
      </a>
    <?php endif; ?>
  </div>

  <!-- ============ FORM LỌC ============ -->
  <form method="get" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    <input type="text" name="q" value="<?= e($q) ?>"
           placeholder="Tìm theo tên sản phẩm..."
           class="<?= UI_INPUT ?> sm:col-span-2">

    <select name="category_id" class="<?= UI_INPUT ?>">
      <option value="0">Tất cả danh mục</option>
      <?php foreach ($categories as $c): ?>
        <option value="<?= (int)$c['id'] ?>" <?= $category_id === (int)$c['id'] ? 'selected' : '' ?>>
          <?= e($c['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <select name="brand" class="<?= UI_INPUT ?>">
      <option value="">Tất cả hãng</option>
      <?php foreach ($brands as $b): ?>
        <option value="<?= e($b) ?>" <?= $brand === $b ? 'selected' : '' ?>>
          <?= e($b) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <select name="ram" class="<?= UI_INPUT ?>">
      <option value="0">Tất cả RAM</option>
      <?php foreach ([8, 12, 16, 32, 64] as $r): ?>
        <option value="<?= $r ?>" <?= $ram === $r ? 'selected' : '' ?>><?= $r ?>GB RAM</option>
      <?php endforeach; ?>
    </select>

    <select name="ssd" class="<?= UI_INPUT ?>">
      <option value="0">Tất cả ổ cứng</option>
      <?php foreach ([256, 512, 1024, 2048, 3072] as $s): ?>
        <option value="<?= $s ?>" <?= $ssd === $s ? 'selected' : '' ?>>
          <?= $s >= 1024 ? ($s / 1024) . 'TB' : $s . 'GB' ?> SSD
        </option>
      <?php endforeach; ?>
    </select>

    <input type="number" name="price_min" min="0" step="1000"
           value="<?= $price_min ?: '' ?>"
           placeholder="Giá từ (đ)" class="<?= UI_INPUT ?>">

    <input type="number" name="price_max" min="0" step="1000"
           value="<?= $price_max ?: '' ?>"
           placeholder="Giá đến (đ)" class="<?= UI_INPUT ?>">

    <select name="sort" class="<?= UI_INPUT ?>">
      <option value="newest"     <?= $sort === 'newest'     ? 'selected' : '' ?>>Mới nhất</option>
      <option value="price_asc"  <?= $sort === 'price_asc'  ? 'selected' : '' ?>>Giá tăng dần</option>
      <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Giá giảm dần</option>
      <option value="name_asc"   <?= $sort === 'name_asc'   ? 'selected' : '' ?>>Tên A→Z</option>
    </select>

    <button type="submit" class="<?= ui_btn() ?>">Áp dụng</button>
  </form>

  <!-- ============ KẾT QUẢ ============ -->
  <?php if (!$products): ?>
    <div class="<?= UI_ALERT ?> text-center py-10">
      <p class="mb-2 font-semibold">Không tìm thấy sản phẩm nào phù hợp.</p>
      <p class="text-sm text-muted-foreground mb-4">Thử xóa bớt bộ lọc hoặc tìm với từ khóa khác.</p>
      <a href="index.php" class="<?= ui_btn() ?> inline-block">Xóa bộ lọc</a>
    </div>
  <?php else: ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
      <?php foreach ($products as $p): ?>
        <a href="show.php?id=<?= (int)$p['id'] ?>"
           class="<?= UI_CARD ?> hover:shadow-md transition flex flex-col">
          <img src="<?= e($p['image'] ?: '/uploads/products/placeholder.jpg') ?>"
               alt="<?= e($p['name']) ?>"
               class="h-44 w-full object-cover bg-muted">
          <div class="px-4 py-3 flex flex-col flex-1">
            <div class="flex items-center gap-2 mb-1">
              <?php if (!empty($p['brand'])): ?>
                <span class="<?= ui_badge() ?>"><?= e($p['brand']) ?></span>
              <?php endif; ?>
              <?php if (!empty($p['category_name'])): ?>
                <span class="text-xs text-muted-foreground"><?= e($p['category_name']) ?></span>
              <?php endif; ?>
            </div>
            <h3 class="font-semibold text-sm line-clamp-2 min-h-[2.5rem]">
              <?= e($p['name']) ?>
            </h3>
            <p class="text-xs text-muted-foreground mt-1">
              <?= (int)$p['ram_gb'] ?>GB RAM
              · <?= (int)$p['ssd_gb'] >= 1024 ? ((int)$p['ssd_gb'] / 1024) . 'TB' : (int)$p['ssd_gb'] . 'GB' ?> SSD
            </p>
            <div class="mt-auto pt-2">
              <span class="text-primary font-bold"><?= number_format($p['price']) ?>đ</span>
              <?php if (!empty($p['old_price'])): ?>
                <span class="text-sm text-muted-foreground line-through ml-1">
                  <?= number_format($p['old_price']) ?>đ
                </span>
              <?php endif; ?>
            </div>
            <?php if ((int)$p['stock'] <= 0): ?>
              <span class="text-xs text-destructive mt-1">Hết hàng</span>
            <?php endif; ?>
          </div>
        </a>
      <?php endforeach; ?>
    </div>

    <!-- ============ PHÂN TRANG ============ -->
    <?php if ($total_pages > 1): ?>
      <nav class="mt-8 flex items-center justify-center flex-wrap gap-1">
        <!-- Prev -->
        <?php if ($page > 1): ?>
          <a href="<?= e($page_url($page - 1)) ?>" class="<?= ui_btn('outline') ?> text-sm">← Trước</a>
        <?php else: ?>
          <span class="<?= ui_btn('outline') ?> text-sm opacity-50 pointer-events-none">← Trước</span>
        <?php endif; ?>

        <!-- Số trang -->
        <?php
          $window = 2;
          $start  = max(1, $page - $window);
          $end    = min($total_pages, $page + $window);
        ?>
        <?php if ($start > 1): ?>
          <a href="<?= e($page_url(1)) ?>" class="<?= ui_btn('outline') ?> text-sm">1</a>
          <?php if ($start > 2): ?><span class="px-2 text-muted-foreground">…</span><?php endif; ?>
        <?php endif; ?>

        <?php for ($i = $start; $i <= $end; $i++): ?>
          <?php if ($i === $page): ?>
            <span class="<?= ui_btn() ?> text-sm"><?= $i ?></span>
          <?php else: ?>
            <a href="<?= e($page_url($i)) ?>" class="<?= ui_btn('outline') ?> text-sm"><?= $i ?></a>
          <?php endif; ?>
        <?php endfor; ?>

        <?php if ($end < $total_pages): ?>
          <?php if ($end < $total_pages - 1): ?><span class="px-2 text-muted-foreground">…</span><?php endif; ?>
          <a href="<?= e($page_url($total_pages)) ?>" class="<?= ui_btn('outline') ?> text-sm"><?= $total_pages ?></a>
        <?php endif; ?>

        <!-- Next -->
        <?php if ($page < $total_pages): ?>
          <a href="<?= e($page_url($page + 1)) ?>" class="<?= ui_btn('outline') ?> text-sm">Sau →</a>
        <?php else: ?>
          <span class="<?= ui_btn('outline') ?> text-sm opacity-50 pointer-events-none">Sau →</span>
        <?php endif; ?>
      </nav>
    <?php endif; ?>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
