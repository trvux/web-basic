<?php
require_once __DIR__ . '/../../includes/admin_header.php';

// READ: danh sách sản phẩm
$products = $pdo->query(
    'SELECT p.*, c.name AS category_name
     FROM products p LEFT JOIN categories c ON p.category_id = c.id
     ORDER BY p.id DESC'
)->fetchAll();
?>

<div class="mb-5 flex items-center justify-between">
  <h1 class="text-2xl font-semibold tracking-tight">Quản lý sản phẩm</h1>
  <a href="create.php" class="<?= ui_btn() ?>">+ Thêm sản phẩm</a>
</div>

<div class="<?= UI_TABLE_WRAP ?>">
  <table class="<?= UI_TABLE ?>">
    <thead>
      <tr class="<?= UI_TR ?>">
        <th class="<?= UI_TH ?>">ID</th>
        <th class="<?= UI_TH ?>">Tên</th>
        <th class="<?= UI_TH ?>">Giá</th>
        <th class="<?= UI_TH ?>">Kho</th>
        <th class="<?= UI_TH ?>">Danh mục</th>
        <th class="<?= UI_TH ?> text-right">Hành động</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($products as $p): ?>
        <tr class="<?= UI_TR ?>">
          <td class="<?= UI_TD ?>"><?= $p['id'] ?></td>
          <td class="<?= UI_TD ?> font-medium"><?= e($p['name']) ?></td>
          <td class="<?= UI_TD ?>"><?= number_format($p['price']) ?>đ</td>
          <td class="<?= UI_TD ?>"><?= $p['stock'] ?></td>
          <td class="<?= UI_TD ?>">
            <?php if ($p['category_name']): ?>
              <span class="<?= ui_badge('secondary') ?>"><?= e($p['category_name']) ?></span>
            <?php endif; ?>
          </td>
          <td class="<?= UI_TD ?> text-right">
            <a href="edit.php?id=<?= $p['id'] ?>" class="<?= ui_btn('outline', 'sm') ?>">Sửa</a>
            <a href="delete.php?id=<?= $p['id'] ?>" class="<?= ui_btn('destructive', 'sm') ?>"
               onclick="return confirm('Xoá sản phẩm này?')">Xoá</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
