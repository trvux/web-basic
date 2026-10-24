<?php
require_once __DIR__ . '/../includes/header.php';

[$items, $total] = cart_items($pdo);
?>

<h1 class="<?= UI_H1 ?>">Giỏ hàng</h1>

<?php if (!$items): ?>
  <div class="<?= UI_CARD ?> items-center text-center">
    <p class="text-muted-foreground">Giỏ hàng trống</p>
    <a href="/index.php" class="<?= ui_btn() ?>">Tiếp tục mua sắm</a>
  </div>
<?php else: ?>
  <form method="post" action="update.php">
    <div class="<?= UI_TABLE_WRAP ?>">
      <table class="<?= UI_TABLE ?>">
        <thead>
          <tr class="<?= UI_TR ?>">
            <th class="<?= UI_TH ?>">Sản phẩm</th>
            <th class="<?= UI_TH ?>">Đơn giá</th>
            <th class="<?= UI_TH ?>">Số lượng</th>
            <th class="<?= UI_TH ?>">Thành tiền</th>
            <th class="<?= UI_TH ?>"></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($items as $p): ?>
            <tr class="<?= UI_TR ?>">
              <td class="<?= UI_TD ?> font-medium"><?= e($p['name']) ?></td>
              <td class="<?= UI_TD ?>"><?= number_format($p['price']) ?>đ</td>
              <td class="<?= UI_TD ?>">
                <input type="number" name="qty[<?= $p['id'] ?>]" value="<?= $p['qty'] ?>"
                       min="0" max="<?= $p['stock'] ?>" class="<?= UI_INPUT ?> w-20!">
              </td>
              <td class="<?= UI_TD ?> font-medium"><?= number_format($p['subtotal']) ?>đ</td>
              <td class="<?= UI_TD ?> text-right">
                <a href="remove.php?id=<?= $p['id'] ?>" class="<?= ui_btn('destructive', 'sm') ?>">Xoá</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
      <p class="text-lg">Tổng cộng: <b class="text-primary"><?= number_format($total) ?>đ</b></p>
      <div class="flex gap-2">
        <button type="submit" class="<?= ui_btn('outline') ?>">Cập nhật số lượng</button>
        <a href="checkout.php" class="<?= ui_btn() ?>">Đặt hàng</a>
      </div>
    </div>
  </form>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
