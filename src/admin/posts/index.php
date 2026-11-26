<?php
require_once __DIR__ . '/../../includes/admin_header.php';
$posts = $pdo->query('SELECT * FROM posts ORDER BY id DESC')->fetchAll();
?>

<div class="mb-5 flex items-center justify-between">
  <h1 class="text-2xl font-semibold tracking-tight">Quản lý bài viết</h1>
  <a href="create.php" class="<?= ui_btn() ?>">+ Thêm bài viết</a>
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
      <?php foreach ($posts as $p): ?>
        <tr class="<?= UI_TR ?>">
          <td class="<?= UI_TD ?>"><?= $p['id'] ?></td>
          <td class="<?= UI_TD ?>">
            <?php if (!empty($p['image'])): ?>
                <img src="<?= e($p['image']) ?>" class="h-12 w-24 object-cover rounded">
            <?php else: ?>
                <span class="text-gray-400 text-sm">Chưa có ảnh</span>
            <?php endif; ?>
          </td>
          <td class="<?= UI_TD ?> font-medium">
            <div class="line-clamp-2 min-w-56"><?= e($p['title']) ?></div>
          </td>
          <td class="<?= UI_TD ?>">
            <?php if (!empty($p['status']) && $p['status'] == 1): ?>
              <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm font-medium">Hiển thị</span>
            <?php else: ?>
              <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-sm font-medium">Đang ẩn</span>
            <?php endif; ?>
          </td>
          <td class="<?= UI_TD ?> text-right whitespace-nowrap">
            <a href="edit.php?id=<?= $p['id'] ?>" class="<?= ui_btn('outline', 'sm') ?>">Sửa</a>
            <form method="post" action="delete.php" class="inline" onsubmit="return confirm('Xoá bài viết này?')">
              <input type="hidden" name="id" value="<?= $p['id'] ?>">
              <button type="submit" class="<?= ui_btn('destructive', 'sm') ?>">Xoá</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>