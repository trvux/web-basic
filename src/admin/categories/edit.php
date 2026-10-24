<?php
require_once __DIR__ . '/../../includes/admin_header.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM categories WHERE id = ?');
$stmt->execute([$id]);
$category = $stmt->fetch();

if (!$category) {
    die('Không tìm thấy danh mục');
}

$error = '';

// UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);

    if ($name === '') {
        $error = 'Tên danh mục không được trống';
    } else {
        $stmt = $pdo->prepare('UPDATE categories SET name = ? WHERE id = ?');
        $stmt->execute([$name, $id]);
        header('Location: index.php');
        exit;
    }
}
?>

<h1 class="<?= UI_H1 ?>">Sửa danh mục #<?= $category['id'] ?></h1>

<div class="<?= UI_CARD ?> max-w-md">
  <form method="post" class="<?= UI_CARD_CONTENT ?> gap-4!">
    <?php if ($error): ?><div class="<?= UI_ALERT_ERROR ?>"><?= e($error) ?></div><?php endif; ?>
    <div class="<?= UI_FIELD ?>">
      <label class="<?= UI_LABEL ?>" for="name">Tên danh mục</label>
      <input class="<?= UI_INPUT ?>" id="name" type="text" name="name" value="<?= e($category['name']) ?>" required>
    </div>
    <div class="flex gap-2">
      <button class="<?= ui_btn() ?>" type="submit">Cập nhật</button>
      <a href="index.php" class="<?= ui_btn('outline') ?>">Huỷ</a>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
