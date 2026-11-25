<?php
require_once __DIR__ . '/../../includes/admin_header.php'; 

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    // Lỗi 4: Validate role
    if ($role !== 'user' && $role !== 'admin') {
        $error = 'Vai trò không hợp lệ. Chỉ nhận "user" hoặc "admin".';
    } elseif (empty($username) || empty($password)) {
        $error = 'Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu.';
    } else {
        // Kiểm tra trùng username
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $checkStmt->execute([$username]);
        if ($checkStmt->fetch()) {
            $error = 'Tên đăng nhập đã tồn tại.';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password, full_name, role) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$username, $hashed_password, $full_name, $role])) {
                header("Location: index.php");
                exit;
            } else {
                $error = 'Có lỗi xảy ra khi thêm người dùng.';
            }
        }
    }
}
?>

<div class="p-6 max-w-lg mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Thêm người dùng mới</h1>

    <!-- Lỗi 5: Dùng UI_ALERT_ERROR -->
    <?php if ($error): ?>
        <div class="<?= UI_ALERT_ERROR ?> mb-4"><?= e($error) ?></div>
    <?php endif; ?>

    <!-- Lỗi 3: Dùng UI_CARD và UI_INPUT, UI_SELECT -->
    <form action="" method="POST" class="<?= UI_CARD ?> p-6">
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Tên đăng nhập</label>
            <input type="text" name="username" class="<?= UI_INPUT ?> w-full" required>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Họ tên</label>
            <input type="text" name="full_name" class="<?= UI_INPUT ?> w-full">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Mật khẩu</label>
            <input type="password" name="password" class="<?= UI_INPUT ?> w-full" required>
        </div>
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Vai trò</label>
            <select name="role" class="<?= UI_SELECT ?> w-full" required>
                <option value="user">User (Khách hàng)</option>
                <option value="admin">Admin (Quản trị viên)</option>
            </select>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="<?= ui_btn('primary') ?>">Lưu người dùng</button>
            <a href="index.php" class="<?= ui_btn('secondary') ?>">Hủy</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>