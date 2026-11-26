<?php
require_once __DIR__ . '/../../includes/admin_header.php';
require_once __DIR__ . '/../../includes/upload.php';

// READ: banner xếp theo thứ tự hiện ở trang chủ
$banners = $pdo->query('SELECT * FROM banners ORDER BY sort_order, id DESC')->fetchAll();
?>

<div class="mb-5 flex items-center justify-between">
  <h1 class="text-2xl font-semibold tracking-tight">Quản lý banner</h1>
  <a href="create.php" class="<?= ui_btn() ?>">+ Thêm banner</a>
</div>

<?php if (!$banners): ?>
  <div class="<?= UI_EMPTY ?>">Chưa có banner nào.</div>
<?php else: ?>
  <div class="<?= UI_TABLE_WRAP ?>">
    <table class="<?= UI_TABLE ?>">
      <thead>
        <tr class="<?= UI_TR ?>">
          <th class="<?= UI_TH ?>">Thứ tự</th>
          <th class="<?= UI_TH ?>">Hình ảnh</th>
          <th class="<?= UI_TH ?>">Tiêu đề</th>
          <th class="<?= UI_TH ?>">Trạng thái</th>
          <th class="<?= UI_TH ?> text-right">Hành động</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($banners as $b): ?>
          <tr class="<?= UI_TR ?>">
            <td class="<?= UI_TD ?>"><?= (int)$b['sort_order'] ?></td>
            <td class="<?= UI_TD ?>"><img src="<?= e(upload_url($b['image'], 'banners')) ?>" alt="<?= e($b['title']) ?>" class="h-12 w-36 min-w-36 rounded bg-muted object-cover"></td>
            <td class="<?= UI_TD ?> font-medium">
              <div class="line-clamp-2 min-w-40"><?= e($b['title']) ?></div>
              <?php if ($b['link']): ?><div class="text-xs font-normal text-muted-foreground"><?= e($b['link']) ?></div><?php endif; ?>
            </td>
            <td class="<?= UI_TD ?> whitespace-nowrap">
              <span class="<?= ui_badge($b['is_active'] ? 'default' : 'secondary') ?>"><?= $b['is_active'] ? 'Hiển thị' : 'Đang ẩn' ?></span>
            </td>
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
<?php endif; ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
