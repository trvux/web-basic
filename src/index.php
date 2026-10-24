<?php
require_once 'includes/header.php';

// READ: lấy sản phẩm kèm tên danh mục. Có thể lọc theo danh mục (?cat=2) và tìm kiếm (?q=asus)
$cat = (int)($_GET['cat'] ?? 0);
$q = trim($_GET['q'] ?? '');

$sql = 'SELECT p.*, c.name AS category_name
        FROM products p LEFT JOIN categories c ON p.category_id = c.id
        WHERE 1=1';
$params = [];
if ($cat) {
    $sql .= ' AND p.category_id = ?';
    $params[] = $cat;
}
if ($q !== '') {
    $sql .= ' AND p.name LIKE ?';
    $params[] = '%' . $q . '%';
}
$sql .= ' ORDER BY p.id DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$categories = $pdo->query('SELECT * FROM categories')->fetchAll();
?>

<h1 class="<?= UI_H1 ?>">Sản phẩm</h1>

<form method="get" class="mb-6 flex flex-wrap gap-2">
  <input type="text" name="q" placeholder="Tìm sản phẩm..." value="<?= e($q) ?>" class="<?= UI_INPUT ?> max-w-xs">
  <select name="cat" class="<?= UI_INPUT ?> max-w-52">
    <option value="0">Tất cả danh mục</option>
    <?php foreach ($categories as $c): ?>
      <option value="<?= $c['id'] ?>" <?= $c['id'] == $cat ? 'selected' : '' ?>><?= e($c['name']) ?></option>
    <?php endforeach; ?>
  </select>
  <button type="submit" class="<?= ui_btn() ?>">Lọc</button>
</form>

<?php if (!$products): ?><p class="text-muted-foreground">Không có sản phẩm nào.</p><?php endif; ?>

<div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
  <?php foreach ($products as $p): ?>
    <div class="<?= UI_CARD ?> pt-0! gap-3!">
      <img src="<?= e($p['image']) ?>" alt="<?= e($p['name']) ?>" class="aspect-square w-full bg-muted object-cover">
      <div class="<?= UI_CARD_CONTENT ?> gap-1.5! flex-1">
        <?php if ($p['category_name']): ?>
          <span class="<?= ui_badge('secondary') ?>"><?= e($p['category_name']) ?></span>
        <?php endif; ?>
        <h3 class="<?= UI_CARD_TITLE ?> line-clamp-2"><?= e($p['name']) ?></h3>
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

<?php require_once 'includes/footer.php'; ?>
