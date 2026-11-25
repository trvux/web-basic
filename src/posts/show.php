<?php
require_once __DIR__ . '/../includes/header.php';

$stmt = $pdo->prepare('SELECT * FROM posts WHERE id = ?');
$stmt->execute([(int)($_GET['id'] ?? 0)]);
$post = $stmt->fetch();
if (!$post) http_response_code(404);
?>

<?php if (!$post): ?>
  <div class="<?= UI_EMPTY ?>">
    <div class="<?= UI_EMPTY_TITLE ?>">Không tìm thấy bài viết</div>
    <a href="index.php" class="<?= ui_btn('outline') ?> mt-3">Về trang tin tức</a>
  </div>
<?php else: ?>
  <?= ui_breadcrumb(['Trang chủ' => '/index.php', 'Tin tức' => '/posts/index.php', $post['title'] => null]) ?>
  <article class="mx-auto grid max-w-3xl gap-4">
    <h1 class="text-3xl font-semibold tracking-tight"><?= e($post['title']) ?></h1>
    <span class="text-sm text-muted-foreground"><?= e(date('d/m/Y', strtotime($post['created_at']))) ?></span>
    <?php if ($post['image']): ?>
      <img src="/uploads/posts/<?= e(rawurlencode(basename($post['image']))) ?>" alt="<?= e($post['title']) ?>" class="w-full rounded-xl bg-muted object-cover">
    <?php endif; ?>
    <div class="leading-relaxed break-words"><?= nl2br(e($post['content'])) ?></div>
  </article>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
