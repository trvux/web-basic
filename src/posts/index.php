<?php
// Danh sách bài viết cho khách. Bài viết do admin quản lý ở admin/posts/.
require_once __DIR__ . '/../includes/header.php';

$perPage = 9;
$total = (int)$pdo->query('SELECT COUNT(*) FROM posts')->fetchColumn();
$pages = max(1, (int)ceil($total / $perPage));
$page = max(1, min((int)($_GET['page'] ?? 1), $pages));
$offset = ($page - 1) * $perPage;
$posts = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC, id DESC LIMIT $perPage OFFSET $offset")->fetchAll();
?>

<h1 class="<?= UI_H1 ?>">Tin tức</h1>

<?php if (!$posts): ?><div class="<?= UI_EMPTY ?>">Chưa có bài viết nào.</div><?php endif; ?>

<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
  <?php foreach ($posts as $post): ?>
    <a href="show.php?id=<?= $post['id'] ?>" class="<?= UI_CARD ?> pt-0! gap-3! transition-shadow hover:shadow-md">
      <?php if ($post['image']): ?>
        <img src="/uploads/posts/<?= e(rawurlencode(basename($post['image']))) ?>" alt="<?= e($post['title']) ?>" loading="lazy" class="aspect-video w-full bg-muted object-cover">
      <?php else: ?>
        <div class="aspect-video w-full bg-muted"></div>
      <?php endif; ?>
      <div class="<?= UI_CARD_CONTENT ?> gap-1.5!">
        <h2 class="<?= UI_CARD_TITLE ?> line-clamp-2"><?= e($post['title']) ?></h2>
        <p class="<?= UI_CARD_DESC ?> line-clamp-3"><?= e($post['content']) ?></p>
        <span class="text-xs text-muted-foreground"><?= e(date('d/m/Y', strtotime($post['created_at']))) ?></span>
      </div>
    </a>
  <?php endforeach; ?>
</div>

<?= ui_pagination($page, $pages) ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
