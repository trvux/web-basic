<?php
require_once __DIR__ . '/../../includes/admin_header.php';

// READ: kèm số sản phẩm của mỗi danh mục
$categories = $pdo->query(
    'SELECT c.*, COUNT(p.id) AS product_count
     FROM categories c LEFT JOIN products p ON p.category_id = c.id
     GROUP BY c.id ORDER BY c.id'
)->fetchAll();
?>

<div class="mb-5 flex items-center justify-between">
  <h1 class="text-2xl font-semibold tracking-tight">Quản lý danh mục</h1>
  <a href="create.php" class="<?= ui_btn() ?>">+ Thêm danh mục</a>
</div>

<div class="<?= UI_TABLE_WRAP ?>">
  <table class="<?= UI_TABLE ?>">
    <thead>
      <tr class="<?= UI_TR ?>">
        <th class="<?= UI_TH ?>">ID</th>
        <th class="<?= UI_TH ?>">Tên</th>
        <th class="<?= UI_TH ?>">Số sản phẩm</th>
        <th class="<?= UI_TH ?> text-right">Hành động</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($categories as $c): ?>
        <tr class="<?= UI_TR ?>">
          <td class="<?= UI_TD ?>"><?= $c['id'] ?></td>
          <td class="<?= UI_TD ?> font-medium"><?= e($c['name']) ?></td>
          <td class="<?= UI_TD ?>"><?= $c['product_count'] ?></td>
          <td class="<?= UI_TD ?> text-right">
            <a href="edit.php?id=<?= $c['id'] ?>" class="<?= ui_btn('outline', 'sm') ?>">Sửa</a>
            <a href="delete.php?id=<?= $c['id'] ?>" class="<?= ui_btn('destructive', 'sm') ?>"
               onclick="return confirm('Xoá danh mục này?')">Xoá</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
