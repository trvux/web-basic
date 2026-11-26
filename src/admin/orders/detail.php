<?php
require_once '/var/www/html/includes/admin_header.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header("Location: index.php");
    exit;
}

// Xử lý đổi trạng thái (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $new_status = $_POST['status'];
    $valid_statuses = ['new', 'shipping', 'done', 'cancelled'];
    
    if (in_array($new_status, $valid_statuses)) {
        $updateStmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $updateStmt->execute([$new_status, $id]);
        $success = "Cập nhật trạng thái thành công!";
    } else {
        $error = "Trạng thái không hợp lệ.";
    }
}

$stmt = $pdo->prepare("SELECT o.*, u.username, u.full_name FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE o.id = ?");
$stmt->execute([$id]);
$order = $stmt->fetch();

if (!$order) {
    echo '<div class="mb-4"><div class="' . UI_EMPTY . '">Đơn hàng không tồn tại.</div></div>';
    require_once __DIR__ . '/../../includes/footer.php';
    exit;
}

$itemStmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$itemStmt->execute([$id]);
$items = $itemStmt->fetchAll();
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="<?= UI_H1 ?>">Chi tiết đơn hàng #<?= e($order['id']) ?></h1>
    <a href="index.php" class="<?= ui_btn('outline') ?>">Quay lại danh sách</a>
</div>

<?php if (isset($success)): ?>
    <!-- Đã sửa dòng 48: Dùng token bg-secondary thay cho bg-green -->
    <div class="mb-4 p-4 rounded border bg-secondary text-secondary-foreground"><?= e($success) ?></div>
<?php endif; ?>
<?php if (isset($error)): ?>
    <div class="<?= UI_ALERT_ERROR ?> mb-4"><?= e($error) ?></div>
<?php endif; ?>

<div class="mb-8 grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="<?= UI_CARD ?> p-4">
        <h2 class="font-bold text-lg mb-2">Thông tin khách hàng</h2>
        <p><strong>Tên đăng nhập:</strong> <?= e($order['username'] ?? 'N/A') ?></p>
        <p><strong>Họ tên:</strong> <?= e($order['full_name'] ?? 'N/A') ?></p>
        <p><strong>Ngày đặt:</strong> <?= e($order['created_at'] ?? '') ?></p>
        <!-- Đã sửa dòng 63: Dùng token text-primary thay cho text-red -->
        <p><strong>Tổng tiền:</strong> <span class="text-primary font-bold"><?= number_format($order['total'] ?? 0, 0, ',', '.') ?> đ</span></p>
    </div>

    <div class="<?= UI_CARD ?> p-4">
        <h2 class="font-bold text-lg mb-2">Cập nhật trạng thái</h2>
        <form action="" method="POST" class="flex gap-4 items-end">
            <div class="flex-1">
                <label class="<?= UI_LABEL ?>">Trạng thái hiện tại</label>
                <select name="status" class="<?= UI_SELECT ?> w-full">
                    <option value="new" <?= $order['status'] === 'new' ? 'selected' : '' ?>>New (Mới)</option>
                    <option value="shipping" <?= $order['status'] === 'shipping' ? 'selected' : '' ?>>Shipping (Đang giao)</option>
                    <option value="done" <?= $order['status'] === 'done' ? 'selected' : '' ?>>Done (Hoàn thành)</option>
                    <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled (Đã hủy)</option>
                </select>
            </div>
            <button type="submit" class="<?= ui_btn() ?>">Cập nhật</button>
        </form>
    </div>
</div>

<h2 class="font-bold text-xl mb-4">Danh sách sản phẩm</h2>
<div class="<?= UI_TABLE_WRAP ?>">
    <table class="<?= UI_TABLE ?>">
        <thead>
            <tr class="<?= UI_TR ?>">
                <th class="<?= UI_TH ?>">Sản phẩm</th>
                <th class="<?= UI_TH ?>">Số lượng</th>
                <th class="<?= UI_TH ?>">Đơn giá</th>
                <th class="<?= UI_TH ?>">Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr class="<?= UI_TR ?>">
                <td class="<?= UI_TD ?>"><?= e($item['name'] ?? 'Sản phẩm đã xóa') ?></td>
                <td class="<?= UI_TD ?>"><?= e($item['qty']) ?></td>
                <td class="<?= UI_TD ?>"><?= number_format($item['price'] ?? 0, 0, ',', '.') ?> đ</td>
                <td class="<?= UI_TD ?>"><?= number_format(($item['price'] ?? 0) * ($item['qty'] ?? 0), 0, ',', '.') ?> đ</td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($items)): ?>
            <tr>
                <td colspan="4" class="<?= UI_TD ?> text-center">
                    <div class="<?= UI_EMPTY ?>">Không có sản phẩm nào trong đơn hàng này.</div>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once '/var/www/html/includes/footer.php';