<?php
require_once 'includes/header.php';

// READ: lấy sản phẩm kèm tên danh mục. Có thể lọc theo danh mục (?cat=2) và tìm kiếm (?q=asus)
$cat = (int)($_GET['cat'] ?? 0);
$q = trim($_GET['q'] ?? '');

$where = ' WHERE 1=1';
$params = [];
if ($cat) {
    $where .= ' AND p.category_id = ?';
    $params[] = $cat;
}
if ($q !== '') {
    $where .= ' AND p.name LIKE ?';
    $params[] = '%' . $q . '%';
}

// Phân trang: đếm tổng trước, rồi chỉ lấy đúng một trang
$perPage = 12;
$stmt = $pdo->prepare('SELECT COUNT(*) FROM products p' . $where);
$stmt->execute($params);
$total = (int)$stmt->fetchColumn();
$pages = max(1, (int)ceil($total / $perPage));
$page = max(1, min((int)($_GET['page'] ?? 1), $pages));
$offset = ($page - 1) * $perPage;

// $perPage và $offset là số nguyên do server tính nên nối vào SQL được
$stmt = $pdo->prepare(
    'SELECT p.*, c.name AS category_name
     FROM products p LEFT JOIN categories c ON p.category_id = c.id'
    . $where . " ORDER BY p.id DESC LIMIT $perPage OFFSET $offset"
);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Banner do admin quản lý ở admin/banners/. Chỉ hiện ở trang đầu, khi không lọc.
$slides = [];
if ($page === 1 && !$cat && $q === '') {
    $banners = $pdo->query('SELECT * FROM banners WHERE is_active = 1 ORDER BY sort_order, id DESC')->fetchAll();
    foreach ($banners as $banner) {
        $slide = '<img src="/uploads/banners/' . e(rawurlencode(basename($banner['image']))) . '" alt="' . e($banner['title'])
               . '" class="aspect-[3/1] w-full bg-muted object-cover">';
        // Chỉ nhận link nội bộ hoặc http(s), chặn kiểu javascript:
        if ($banner['link'] && preg_match('#^(https?://|/)#i', $banner['link'])) {
            $slide = '<a href="' . e($banner['link']) . '">' . $slide . '</a>';
        }
        $slides[] = $slide;
    }
}

$categories = $pdo->query('SELECT * FROM categories')->fetchAll();
?>

<?php if ($slides): ?><div class="mb-6"><?= ui_carousel($slides, 5) ?></div><?php endif; ?>

<h1 class="<?= UI_H1 ?>">Sản phẩm</h1>

<form method="get" class="mb-6 flex flex-wrap gap-2">
  <input type="text" name="q" aria-label="Tìm sản phẩm" placeholder="Tìm sản phẩm..." value="<?= e($q) ?>" class="<?= UI_INPUT ?> max-w-xs">
  <select name="cat" aria-label="Danh mục" class="<?= UI_SELECT ?> max-w-52">
    <option value="0">Tất cả danh mục</option>
    <?php foreach ($categories as $c): ?>
      <option value="<?= $c['id'] ?>" <?= $c['id'] == $cat ? 'selected' : '' ?>><?= e($c['name']) ?></option>
    <?php endforeach; ?>
  </select>
  <button type="submit" class="<?= ui_btn() ?>">Lọc</button>
</form>

<?php if (!$products): ?><div class="<?= UI_EMPTY ?>">Không có sản phẩm nào.</div><?php endif; ?>

<div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
  <?php foreach ($products as $p): ?>
    <div class="<?= UI_CARD ?> pt-0! gap-3!">
      <a href="/products/show.php?id=<?= $p['id'] ?>">
        <img src="<?= e($p['image']) ?>" alt="<?= e($p['name']) ?>" loading="lazy" class="aspect-square w-full bg-muted object-cover">
      </a>
      <div class="<?= UI_CARD_CONTENT ?> gap-1.5! flex-1">
        <?php if ($p['category_name']): ?>
          <span class="<?= ui_badge('secondary') ?>"><?= e($p['category_name']) ?></span>
        <?php endif; ?>
        <h3 class="<?= UI_CARD_TITLE ?> line-clamp-2"><a href="/products/show.php?id=<?= $p['id'] ?>" class="hover:text-primary"><?= e($p['name']) ?></a></h3>
        <p class="<?= UI_CARD_DESC ?> line-clamp-2"><?= e($p['description']) ?></p>
        <p class="mt-auto pt-1 text-lg font-bold text-primary"><?= number_format($p['price']) ?>đ</p>
      </div>
      <div class="px-6">
        <?php if ($p['stock'] > 0): ?>
          <a href="/cart/add.php?id=<?= $p['id'] ?>" class="<?= ui_btn() ?> w-full">Thêm vào giỏ</a>
        <?php else: ?>
          <span class="<?= ui_btn('secondary') ?> w-full opacity-60">Hết hàng</span>
        <?php endif; ?>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<?= ui_pagination($page, $pages, array_filter(['q' => $q, 'cat' => $cat], fn($v) => $v !== '' && $v !== 0)) ?>

<?php require_once 'includes/footer.php'; ?>
