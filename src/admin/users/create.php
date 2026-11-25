<?php
require_once __DIR__ . '/../../includes/admin_header.php'; 

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Vui lòng nhập tên đăng nhập và mật khẩu.';
    } elseif (strlen($password) < 6) {
        $error = 'Mật khẩu tối thiểu 6 ký tự.';
    } elseif ($role !== 'user' && $role !== 'admin') {
        $error = 'Vai trò không hợp lệ.';
    } else {
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $checkStmt->execute([$username]);
        if ($checkStmt->fetch()) {
            $error = 'Tên đăng nhập này đã được sử dụng.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, full_name, password, role) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$username, $full_name, $hash, $role])) {
                header("Location: index.php");
                exit;
            } else {
                $error = 'Có lỗi xảy ra khi thêm người dùng.';
            }
        }
    }
}
?>

<h1 class="<?= UI_H1 ?>">Thêm người dùng mới</h1>

<div class="<?= UI_CARD ?> max-w-md">
<form action="" method="POST" class="<?= UI_CARD_CONTENT ?> gap-4!">
    <?php if ($error): ?><div class="<?= UI_ALERT_ERROR ?>"><?= e($error) ?></div><?php endif; ?>
    <div class="<?= UI_FIELD ?>">
        <label class="<?= UI_LABEL ?>">Tên đăng nhập (username)</label>
        <input type="text" name="username" class="<?= UI_INPUT ?>" required>
    </div>
    <div class="<?= UI_FIELD ?>">
        <label class="<?= UI_LABEL ?>">Họ và tên (full_name)</label>
        <input type="text" name="full_name" class="<?= UI_INPUT ?>">
    </div>
    <div class="<?= UI_FIELD ?>">
        <label class="<?= UI_LABEL ?>">Mật khẩu</label>
        <input type="password" name="password" class="<?= UI_INPUT ?>" minlength="6" required>
    </div>
    <div class="<?= UI_FIELD ?>">
        <label class="<?= UI_LABEL ?>">Vai trò</label>
        <select name="role" class="<?= UI_SELECT ?>" required>
            <option value="user">User (Khách)</option>
            <option value="admin">Admin (Quản trị)</option>
        </select>
    </div>
    <div class="flex gap-3">
        <button type="submit" class="<?= ui_btn() ?>">Lưu người dùng</button>
        <a href="index.php" class="<?= ui_btn('outline') ?>">Hủy</a>
    </div>
</form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>