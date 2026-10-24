<?php
require_once __DIR__ . '/../../includes/admin_header.php';

// Tất cả đơn hàng kèm tên người mua
$orders = $pdo->query(
    'SELECT o.*, u.username
     FROM orders o LEFT JOIN users u ON o.user_id = u.id
     ORDER BY o.id DESC'
)->fetchAll();
?>

<h1 class="<?= UI_H1 ?>">Đơn hàng</h1>

<?php if (!$orders): ?><p class="text-muted-foreground">Chưa có đơn hàng nào.</p><?php endif; ?>

<div class="grid gap-4">
  <?php foreach ($orders as $o): ?>
    <?php
      $stmt = $pdo->prepare('SELECT * FROM order_items WHERE order_id = ?');
      $stmt->execute([$o['id']]);
      $lines = $stmt->fetchAll();
    ?>
    <div class="<?= UI_CARD ?>">
      <div class="<?= UI_CARD_HEADER ?>">
        <h3 class="<?= UI_CARD_TITLE ?>">Đơn #<?= $o['id'] ?> - <?= e($o['username']) ?></h3>
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

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
