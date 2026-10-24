<?php
require_once __DIR__ . '/../config/db.php';
require_login(); // phải đăng nhập mới đặt hàng được

[$items, $total] = cart_items($pdo);
if (!$items) {
    header('Location: index.php');
    exit;
}

$error = '';

// Khi bấm "Xác nhận đặt hàng"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Transaction: hoặc tạo đủ đơn + trừ kho, hoặc không làm gì cả
    $pdo->beginTransaction();
    try {
        foreach ($items as $p) {
            // khoá dòng sản phẩm để 2 người không mua trùng hàng cuối cùng
            $stmt = $pdo->prepare('SELECT stock FROM products WHERE id = ? FOR UPDATE');
            $stmt->execute([$p['id']]);
            $stock = $stmt->fetchColumn();
            if ($stock < $p['qty']) {
                throw new Exception('Sản phẩm "' . $p['name'] . '" không đủ hàng (còn ' . $stock . ')');
            }
        }

        $stmt = $pdo->prepare('INSERT INTO orders (user_id, total) VALUES (?, ?)');
        $stmt->execute([$_SESSION['user']['id'], $total]);
        $orderId = $pdo->lastInsertId();

        foreach ($items as $p) {
            // lưu lại tên + giá lúc mua (sau này sửa giá sản phẩm thì đơn cũ không đổi)
            $stmt = $pdo->prepare(
                'INSERT INTO order_items (order_id, product_id, name, price, qty) VALUES (?, ?, ?, ?, ?)'
            );
            $stmt->execute([$orderId, $p['id'], $p['name'], $p['price'], $p['qty']]);

            $stmt = $pdo->prepare('UPDATE products SET stock = stock - ? WHERE id = ?');
            $stmt->execute([$p['qty'], $p['id']]);
        }

        $pdo->commit();
        unset($_SESSION['cart']);
        header('Location: /orders/index.php');
        exit;
    } catch (Exception $ex) {
        $pdo->rollBack();
        $error = $ex->getMessage();
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<h1 class="<?= UI_H1 ?>">Xác nhận đặt hàng</h1>
<?php if ($error): ?><div class="<?= UI_ALERT_ERROR ?> mb-4"><?= e($error) ?></div><?php endif; ?>

<div class="<?= UI_TABLE_WRAP ?>">
  <table class="<?= UI_TABLE ?>">
    <thead>
      <tr class="<?= UI_TR ?>">
        <th class="<?= UI_TH ?>">Sản phẩm</th>
        <th class="<?= UI_TH ?>">Số lượng</th>
        <th class="<?= UI_TH ?>">Thành tiền</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($items as $p): ?>
        <tr class="<?= UI_TR ?>">
          <td class="<?= UI_TD ?> font-medium"><?= e($p['name']) ?></td>
          <td class="<?= UI_TD ?>"><?= $p['qty'] ?></td>
          <td class="<?= UI_TD ?>"><?= number_format($p['subtotal']) ?>đ</td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<form method="post" class="mt-4 flex flex-wrap items-center justify-between gap-3">
  <p class="text-lg">Tổng cộng: <b class="text-primary"><?= number_format($total) ?>đ</b></p>
  <div class="flex gap-2">
    <a href="index.php" class="<?= ui_btn('outline') ?>">Quay lại giỏ</a>
    <button type="submit" class="<?= ui_btn() ?>">Xác nhận đặt hàng</button>
  </div>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
