<?php
require_once __DIR__ . '/../../includes/admin_header.php';
require_once __DIR__ . '/../../includes/upload.php';

// READ: bài mới nhất lên đầu
$posts = $pdo->query('SELECT * FROM posts ORDER BY id DESC')->fetchAll();
?>

<div class="mb-5 flex items-center justify-between">
  <h1 class="text-2xl font-semibold tracking-tight">Quản lý bài viết</h1>
  <a href="create.php" class="<?= ui_btn() ?>">+ Thêm bài viết</a>
</div>

<?php if (!$posts): ?>
  <div class="<?= UI_EMPTY ?>">Chưa có bài viết nào.</div>
<?php else: ?>
  <div class="<?= UI_TABLE_WRAP ?>">
    <table class="<?= UI_TABLE ?>">
      <thead>
        <tr class="<?= UI_TR ?>">
          <th class="<?= UI_TH ?>">ID</th>
          <th class="<?= UI_TH ?>">Hình ảnh</th>
          <th class="<?= UI_TH ?>">Tiêu đề</th>
          <th class="<?= UI_TH ?>">Ngày đăng</th>
          <th class="<?= UI_TH ?> text-right">Hành động</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($posts as $p): ?>
          <tr class="<?= UI_TR ?>">
            <td class="<?= UI_TD ?>"><?= $p['id'] ?></td>
            <td class="<?= UI_TD ?>">
              <?php if ($p['image']): ?>
                <img src="<?= e(upload_url($p['image'], 'posts')) ?>" alt="<?= e($p['title']) ?>" class="h-12 w-20 min-w-20 rounded bg-muted object-cover">
              <?php else: ?>
                <span class="text-sm text-muted-foreground">Chưa có ảnh</span>
              <?php endif; ?>
            </td>
            <td class="<?= UI_TD ?> font-medium">
              <a href="/posts/show.php?id=<?= $p['id'] ?>" class="line-clamp-2 min-w-48 hover:text-primary"><?= e($p['title']) ?></a>
            </td>
            <td class="<?= UI_TD ?> whitespace-nowrap"><?= e(date('d/m/Y', strtotime($p['created_at']))) ?></td>
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
<?php endif; ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
