<?php
require_once __DIR__ . '/../config/db.php';
require_login(); // redirect phải chạy TRƯỚC khi in bất kỳ HTML nào
require_once __DIR__ . '/../includes/header.php';

// Đơn hàng của user đang đăng nhập
$stmt = $pdo->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC');
$stmt->execute([$_SESSION['user']['id']]);
$orders = $stmt->fetchAll();
?>

<h1 class="<?= UI_H1 ?>">Đơn hàng của tôi</h1>

<?php if (!$orders): ?>
  <p class="text-muted-foreground">Bạn chưa có đơn hàng nào.</p>
<?php endif; ?>

<div class="grid gap-4">
  <?php foreach ($orders as $o): ?>
    <?php
      $stmt = $pdo->prepare('SELECT * FROM order_items WHERE order_id = ?');
      $stmt->execute([$o['id']]);
      $lines = $stmt->fetchAll();
    ?>
    <div class="<?= UI_CARD ?>">
      <div class="<?= UI_CARD_HEADER ?>">
        <h3 class="<?= UI_CARD_TITLE ?>">Đơn #<?= $o['id'] ?></h3>
        <p class="<?= UI_CARD_DESC ?>"><?= e($o['created_at']) ?> - Tổng: <b class="text-primary"><?= number_format($o['total']) ?>đ</b></p>
      </div>
      <div class="<?= UI_CARD_CONTENT ?>">
        <table class="<?= UI_TABLE ?>">
          <thead>
            <tr class="<?= UI_TR ?>">
              <th class="<?= UI_TH ?> px-0!">Sản phẩm</th>
              <th class="<?= UI_TH ?>">Đơn giá</th>
              <th class="<?= UI_TH ?>">SL</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($lines as $l): ?>
              <tr class="<?= UI_TR ?>">
                <td class="<?= UI_TD ?> px-0!"><?= e($l['name']) ?></td>
                <td class="<?= UI_TD ?>"><?= number_format($l['price']) ?>đ</td>
                <td class="<?= UI_TD ?>"><?= $l['qty'] ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
