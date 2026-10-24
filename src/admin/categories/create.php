<?php
require_once __DIR__ . '/../../includes/admin_header.php';

$error = '';

// CREATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);

    if ($name === '') {
        $error = 'Tên danh mục không được trống';
    } else {
        $stmt = $pdo->prepare('INSERT INTO categories (name) VALUES (?)');
        $stmt->execute([$name]);
        header('Location: index.php');
        exit;
    }
}
?>

<h1 class="<?= UI_H1 ?>">Thêm danh mục</h1>

<div class="<?= UI_CARD ?> max-w-md">
  <form method="post" class="<?= UI_CARD_CONTENT ?> gap-4!">
    <?php if ($error): ?><div class="<?= UI_ALERT_ERROR ?>"><?= e($error) ?></div><?php endif; ?>
    <div class="<?= UI_FIELD ?>">
      <label class="<?= UI_LABEL ?>" for="name">Tên danh mục</label>
      <input class="<?= UI_INPUT ?>" id="name" type="text" name="name" required>
    </div>
    <div class="flex gap-2">
      <button class="<?= ui_btn() ?>" type="submit">Lưu</button>
      <a href="index.php" class="<?= ui_btn('outline') ?>">Huỷ</a>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
