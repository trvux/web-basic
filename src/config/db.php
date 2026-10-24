<?php
// Kết nối DB. Mọi file cần DB chỉ việc: require_once __DIR__ . '/../config/db.php';
// Host là "db" (tên service trong docker-compose), KHÔNG phải localhost.
session_start();

// Output buffering: PHP giữ HTML lại đến cuối script rồi mới gửi đi.
// Nhờ vậy header('Location: ...') (redirect) vẫn chạy được dù trang đã include header.php.
// Thiếu dòng này sẽ dính lỗi "headers already sent" và redirect âm thầm không chạy.
ob_start();

// Thông tin kết nối lấy từ biến môi trường (docker-compose.prod.yml đặt khi chạy thật).
// Chạy trên máy dev không đặt gì thì dùng giá trị mặc định bên phải.
$dbHost = getenv('DB_HOST') ?: 'db';
$dbName = getenv('DB_NAME') ?: 'shop';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASS') ?: 'root';

$pdo = new PDO(
    "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4",
    $dbUser,
    $dbPass,
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
);

function is_admin() {
    return !empty($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
}

// Gọi ở đầu mọi trang admin (đã gọi sẵn trong includes/admin_header.php).
function require_admin() {
    if (!is_admin()) {
        header('Location: /auth/login.php');
        exit;
    }
}

// Bắt buộc đăng nhập (dùng cho checkout, xem đơn hàng).
function require_login() {
    if (empty($_SESSION['user'])) {
        header('Location: /auth/login.php');
        exit;
    }
}

// Lấy sản phẩm trong giỏ ($_SESSION['cart'] = [product_id => số lượng]).
// Trả về [danh sách sản phẩm (có thêm qty, subtotal), tổng tiền].
function cart_items($pdo) {
    $cart = $_SESSION['cart'] ?? [];
    $items = [];
    $total = 0;
    if ($cart) {
        $ids = array_keys($cart);
        $in = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($in)");
        $stmt->execute($ids);
        foreach ($stmt->fetchAll() as $p) {
            $p['qty'] = $cart[$p['id']];
            $p['subtotal'] = $p['price'] * $p['qty'];
            $total += $p['subtotal'];
            $items[] = $p;
        }
    }
    return [$items, $total];
}

// Escape output để chống XSS.
function e($s) {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}
