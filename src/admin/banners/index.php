<?php
require_once __DIR__ . '/../../includes/admin_header.php';

// READ: danh sách banner
$banners = $pdo->query(
    'SELECT * FROM banners ORDER BY id DESC'
)->fetchAll();
?>

<div class="mb-5 flex items-center justify-between">
  <h1 class="text-2xl font-semibold tracking-tight">Quản lý banner</h1>
  <a href="create.php" class="<?= ui_btn() ?>">+ Thêm banner</a>
</div>

<div class="<?= UI_TABLE_WRAP ?>">
  <table class="<?= UI_TABLE ?>">
    <thead>
      <tr class="<?= UI_TR ?>">
        <th class="<?= UI_TH ?>">ID</th>
        <th class="<?= UI_TH ?>">Hình ảnh</th>
        <th class="<?= UI_TH ?>">Tiêu đề</th>
        <th class="<?= UI_TH ?>">Trạng thái</th>
        <th class="<?= UI_TH ?> text-right">Hành động</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($banners as $b): ?>
        <tr class="<?= UI_TR ?>">
          <td class="<?= UI_TD ?>"><?= $b['id'] ?></td>
          
          <!-- Cột Hình ảnh: dùng cột image -->
          <td class="<?= UI_TD ?>">
            <?php if (!empty($b['image'])): ?>
                <img src="<?= e($b['image']) ?>" alt="Banner" class="h-12 w-24 object-cover rounded">
            <?php else: ?>
                <span class="text-gray-400 text-sm">Chưa có ảnh</span>
            <?php endif; ?>
          </td>

          <!-- Cột Tiêu đề -->
          <td class="<?= UI_TD ?> font-medium">
            <div class="line-clamp-2 min-w-56"><?= e($b['title']) ?></div>
          </td>

          <!-- Cột Trạng thái: Dùng CSS trực tiếp -->
          <td class="<?= UI_TD ?>">
            <?php if (!empty($b['status']) && $b['status'] == 1): ?>
              <span class="inline-flex items-center justify-center rounded-md px-2.5 py-0.5 text-xs font-medium bg-green-100 text-green-800">Hiển thị</span>
            <?php else: ?>
              <span class="inline-flex items-center justify-center rounded-md px-2.5 py-0.5 text-xs font-medium bg-gray-100 text-gray-600">Đang ẩn</span>
            <?php endif; ?>
          </td>

          <!-- Cột Hành động -->
          <td class="<?= UI_TD ?> text-right whitespace-nowrap">
            <a href="edit.php?id=<?= $b['id'] ?>" class="<?= ui_btn('outline', 'sm') ?>">Sửa</a>
            <form method="post" action="delete.php" class="inline" onsubmit="return confirm('Xoá banner này?')">
              <input type="hidden" name="id" value="<?= $b['id'] ?>">
              <button type="submit" class="<?= ui_btn('destructive', 'sm') ?>">Xoá</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>