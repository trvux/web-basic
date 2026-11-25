<?php
// Trạng thái đơn hàng dùng chung cho trang admin và trang khách.

// mã trong DB => [nhãn tiếng Việt, kiểu ui_badge]
const ORDER_STATUSES = [
    'new'       => ['Mới', 'secondary'],
    'shipping'  => ['Đang giao', 'outline'],
    'done'      => ['Hoàn tất', 'default'],
    'cancelled' => ['Đã huỷ', 'destructive'],
];

function order_status_badge($status) {
    [$label, $variant] = ORDER_STATUSES[$status] ?? [$status, 'secondary'];
    return '<span class="' . ui_badge($variant) . '">' . e($label) . '</span>';
}

// Đổi trạng thái đơn. Huỷ đơn thì trả hàng về kho, mở lại đơn đã huỷ thì trừ kho lần nữa.
// Trả về null nếu thành công, hoặc câu báo lỗi.
function set_order_status($pdo, $orderId, $newStatus) {
    if (!is_string($newStatus) || !isset(ORDER_STATUSES[$newStatus])) {
        return 'Trạng thái không hợp lệ.';
    }

    $pdo->beginTransaction();
    // khoá dòng đơn hàng để bấm hai lần liền không trả kho hai lần
    $stmt = $pdo->prepare('SELECT status FROM orders WHERE id = ? FOR UPDATE');
    $stmt->execute([$orderId]);
    $oldStatus = $stmt->fetchColumn();
    if ($oldStatus === false) {
        $pdo->rollBack();
        return 'Đơn hàng không tồn tại.';
    }

    $cancelling = $newStatus === 'cancelled' && $oldStatus !== 'cancelled';
    $reopening = $oldStatus === 'cancelled' && $newStatus !== 'cancelled';

    if ($cancelling || $reopening) {
        $stmt = $pdo->prepare('SELECT product_id, name, qty FROM order_items WHERE order_id = ? AND product_id IS NOT NULL');
        $stmt->execute([$orderId]);
        foreach ($stmt->fetchAll() as $item) {
            $find = $pdo->prepare('SELECT stock FROM products WHERE id = ? FOR UPDATE');
            $find->execute([$item['product_id']]);
            $stock = $find->fetchColumn();
            if ($stock === false) continue; // sản phẩm đã bị xoá: không còn kho để cộng trừ

            if ($reopening && $stock < $item['qty']) {
                $pdo->rollBack();
                return 'Không mở lại được đơn: "' . $item['name'] . '" không đủ hàng (còn ' . $stock . ').';
            }
            $change = $cancelling ? $item['qty'] : -$item['qty'];
            $pdo->prepare('UPDATE products SET stock = stock + ? WHERE id = ?')->execute([$change, $item['product_id']]);
        }
    }

    $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?')->execute([$newStatus, $orderId]);
    $pdo->commit();
    return null;
}
