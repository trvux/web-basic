<?php
require_once __DIR__ . '/../../includes/admin_header.php'; 

$error = '';
$id = (int)($_GET['id'] ?? 0);

if (!$id) {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    echo '<div class="mb-4">' . UI_EMPTY . 'Người dùng không tồn tại.</div>';
    require_once __DIR__ . '/../../includes/footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] ?? '';
    
    $current_user_id = (int)($_SESSION['user']['id'] ?? 0);

    if (empty($name) || empty($email)) {
        $error = 'Vui lòng nhập đầy đủ thông tin.';
    } elseif ($role !== 'user' && $role !== 'admin') {
        $error = 'Vai trò không hợp lệ.';
    } elseif ($id === $current_user_id && $role !== 'admin') {
        $error = 'Bạn không thể tự hạ quyền chính mình.';
    } else {
        $updateStmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?");
        if ($updateStmt->execute([$name, $email, $role, $id])) {
            header("Location: index.php");
            exit;
        } else {
            $error = 'Có lỗi xảy ra khi cập nhật.';
        }
    }
}
?>

<h1 class="<?= UI_H1 ?> mb-6">Sửa người dùng</h1>

<?php if ($error): ?>
    <div class="mb-4"><?= UI_ALERT_ERROR ?> <?= e($error) ?></div>
<?php endif; ?>

<form action="" method="POST">
    <div class="mb-4">
        <label class="<?= UI_LABEL ?>">Tên người dùng</label>
        <input type="text" name="name" value="<?= e($user['name']) ?>" class="<?= UI_INPUT ?> w-full" required>
    </div>
    <div class="mb-4">
        <label class="<?= UI_LABEL ?>">Email</label>
        <input type="email" name="email" value="<?= e($user['email']) ?>" class="<?= UI_INPUT ?> w-full" required>
    </div>
    <div class="mb-6">
        <label class="<?= UI_LABEL ?>">Vai trò</label>
        <select name="role" class="<?= UI_SELECT ?> w-full" required>
            <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User (Khách)</option>
            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin (Quản trị)</option>
        </select>
    </div>
    <div class="flex gap-3">
        <button type="submit" class="<?= ui_btn() ?>">Cập nhật</button>
        <a href="index.php" class="<?= ui_btn('outline') ?>">Hủy</a>
    </div>
</form>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>