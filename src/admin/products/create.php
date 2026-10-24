<?php
require_once __DIR__ . '/../../includes/admin_header.php';

$categories = $pdo->query('SELECT * FROM categories')->fetchAll();
$error = '';

// CREATE: chạy khi form được submit (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $price = (int)$_POST['price'];
    $stock = (int)$_POST['stock'];
    $description = trim($_POST['description']);
    $image = trim($_POST['image']);
    $category_id = $_POST['category_id'] !== '' ? (int)$_POST['category_id'] : null;

    if ($name === '' || $price <= 0) {
        $error = 'Tên không được trống và giá phải lớn hơn 0';
    } else {
        // Prepared statement: KHÔNG nối chuỗi trực tiếp vào SQL (chống SQL injection)
        $stmt = $pdo->prepare(
            'INSERT INTO products (name, price, stock, description, image, category_id)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$name, $price, $stock, $description, $image, $category_id]);
        header('Location: index.php');
        exit;
    }
}
?>

<h1 class="<?= UI_H1 ?>">Thêm sản phẩm</h1>

<div class="<?= UI_CARD ?> max-w-2xl">
  <form method="post" class="<?= UI_CARD_CONTENT ?> gap-4!">
    <?php if ($error): ?><div class="<?= UI_ALERT_ERROR ?>"><?= e($error) ?></div><?php endif; ?>
    <div class="<?= UI_FIELD ?>">
      <label class="<?= UI_LABEL ?>" for="name">Tên</label>
      <input class="<?= UI_INPUT ?>" id="name" type="text" name="name" required>
    </div>
    <div class="grid grid-cols-2 gap-4">
      <div class="<?= UI_FIELD ?>">
        <label class="<?= UI_LABEL ?>" for="price">Giá (đ)</label>
        <input class="<?= UI_INPUT ?>" id="price" type="number" name="price" required>
      </div>
      <div class="<?= UI_FIELD ?>">
        <label class="<?= UI_LABEL ?>" for="stock">Số lượng kho</label>
        <input class="<?= UI_INPUT ?>" id="stock" type="number" name="stock" value="0">
      </div>
    </div>
    <div class="<?= UI_FIELD ?>">
      <label class="<?= UI_LABEL ?>" for="image">Link ảnh</label>
      <input class="<?= UI_INPUT ?>" id="image" type="text" name="image">
    </div>
    <div class="<?= UI_FIELD ?>">
      <label class="<?= UI_LABEL ?>" for="description">Mô tả</label>
      <textarea class="<?= UI_TEXTAREA ?>" id="description" name="description"></textarea>
    </div>
    <div class="<?= UI_FIELD ?>">
      <label class="<?= UI_LABEL ?>" for="category_id">Danh mục</label>
      <select class="<?= UI_INPUT ?>" id="category_id" name="category_id">
        <option value="">-- Không có --</option>
        <?php foreach ($categories as $c): ?>
          <option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="flex gap-2">
      <button class="<?= ui_btn() ?>" type="submit">Lưu</button>
      <a href="index.php" class="<?= ui_btn('outline') ?>">Huỷ</a>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
