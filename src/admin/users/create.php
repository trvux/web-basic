<?php
require_once __DIR__ . '/../../includes/admin_header.php'; 

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    if (empty($name) || empty($email) || empty($password)) {
        $error = 'Vui lòng nhập đầy đủ thông tin.';
    } elseif ($role !== 'user' && $role !== 'admin') {
        $error = 'Vai trò không hợp lệ.';
    } else {
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $checkStmt->execute([$email]);
        if ($checkStmt->fetch()) {
            $error = 'Email này đã được sử dụng.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$name, $email, $hash, $role])) {
                header("Location: index.php");
                exit;
            } else {
                $error = 'Có lỗi xảy ra khi thêm người dùng.';
            }
        }
    }
}
?>

<h1 class="<?= UI_H1 ?> mb-6">Thêm người dùng mới</h1>

<?php if ($error): ?>
    <div class="mb-4"><?= UI_ALERT_ERROR ?> <?= e($error) ?></div>
<?php endif; ?>

<form action="" method="POST">
    <div class="mb-4">
        <label class="<?= UI_LABEL ?>">Tên người dùng</label>
        <input type="text" name="name" class="<?= UI_INPUT ?> w-full" required>
    </div>
    <div class="mb-4">
        <label class="<?= UI_LABEL ?>">Email</label>
        <input type="email" name="email" class="<?= UI_INPUT ?> w-full" required>
    </div>
    <div class="mb-4">
        <label class="<?= UI_LABEL ?>">Mật khẩu</label>
        <input type="password" name="password" class="<?= UI_INPUT ?> w-full" required>
    </div>
    <div class="mb-6">
        <label class="<?= UI_LABEL ?>">Vai trò</label>
        <select name="role" class="<?= UI_SELECT ?> w-full" required>
            <option value="user">User (Khách)</option>
            <option value="admin">Admin (Quản trị)</option>
        </select>
    </div>
    <div class="flex gap-3">
        <button type="submit" class="<?= ui_btn() ?>">Lưu người dùng</button>
        <a href="index.php" class="<?= ui_btn('outline') ?>">Hủy</a>
    </div>
</form>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>