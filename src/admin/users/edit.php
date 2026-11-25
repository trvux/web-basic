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

// Lỗi 5: Bỏ die(), dùng UI_ALERT_ERROR và footer
if (!$user) {
    echo '<div class="p-6 max-w-lg mx-auto"><div class="' . UI_ALERT_ERROR . '">Người dùng không tồn tại.</div></div>';
    require_once __DIR__ . '/../../includes/footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $role = $_POST['role'] ?? '';
    $password = $_POST['password'] ?? '';

    // Lỗi 4: Validate role
    if ($role !== 'user' && $role !== 'admin') {
        $error = 'Vai trò không hợp lệ.';
    } 
    // Lỗi 1: Chặn tự hạ quyền chính mình
    else {
        $current_user_id = (int)($_SESSION['user']['id'] ?? 0);
        if ($id === $current_user_id && $role !== 'admin') {
            $error = 'Bạn không thể tự hạ quyền (demote) chính mình.';
        } else {
            // Cập nhật
            if (!empty($password)) {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $updateStmt = $pdo->prepare("UPDATE users SET full_name = ?, role = ?, password = ? WHERE id = ?");
                $result = $updateStmt->execute([$full_name, $role, $hashed, $id]);
            } else {
                $updateStmt = $pdo->prepare("UPDATE users SET full_name = ?, role = ? WHERE id = ?");
                $result = $updateStmt->execute([$full_name, $role, $id]);
            }

            if ($result) {
                header("Location: index.php");
                exit;
            } else {
                $error = 'Có lỗi xảy ra khi cập nhật.';
            }
        }
    }
}
?>

<div class="p-6 max-w-lg mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Sửa thông tin người dùng</h1>

    <?php if ($error): ?>
        <div class="<?= UI_ALERT_ERROR ?> mb-4"><?= e($error) ?></div>
    <?php endif; ?>

    <form action="" method="POST" class="<?= UI_CARD ?> p-6">
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Tên đăng nhập (Không được sửa)</label>
            <input type="text" value="<?= e($user['username']) ?>" class="<?= UI_INPUT ?> w-full bg-gray-100 cursor-not-allowed" readonly>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Họ tên</label>
            <input type="text" name="full_name" value="<?= e($user['full_name'] ?? '') ?>" class="<?= UI_INPUT ?> w-full">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Mật khẩu mới (Để trống nếu không đổi)</label>
            <input type="password" name="password" class="<?= UI_INPUT ?> w-full">
        </div>
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Vai trò</label>
            <select name="role" class="<?= UI_SELECT ?> w-full" required>
                <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User (Khách hàng)</option>
                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin (Quản trị viên)</option>
            </select>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="<?= ui_btn('primary') ?>">Cập nhật</button>
            <a href="index.php" class="<?= ui_btn('secondary') ?>">Hủy</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>