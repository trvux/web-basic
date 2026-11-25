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
    echo '<div class="mb-4"><div class="' . UI_EMPTY . '">Người dùng không tồn tại.</div></div>';
    require_once __DIR__ . '/../../includes/footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';
    
    $current_user_id = (int)($_SESSION['user']['id'] ?? 0);

    if (empty($username)) {
        $error = 'Tên đăng nhập không được để trống.';
    } elseif ($role !== 'user' && $role !== 'admin') {
        $error = 'Vai trò không hợp lệ.';
    } elseif ($id === $current_user_id && $role !== 'admin') {
        $error = 'Bạn không thể tự hạ quyền chính mình.';
    } else {
        // ✅ BƯỚC SỬA LỖI: Kiểm tra trùng username với người dùng KHÁC
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $checkStmt->execute([$username, $id]);
        if ($checkStmt->fetch()) {
            $error = 'Tên đăng nhập này đã được người dùng khác sử dụng.';
        } else {
            // Xây dựng câu SQL cập nhật
            $sql = "UPDATE users SET username = ?, full_name = ?, role = ?";
            $params = [$username, $full_name, $role];

            // Nếu có nhập mật khẩu mới thì mới cập nhật mật khẩu
            if (!empty($password)) {
                $sql .= ", password = ?";
                $params[] = password_hash($password, PASSWORD_DEFAULT);
            }
            
            $sql .= " WHERE id = ?";
            $params[] = $id;

            $updateStmt = $pdo->prepare($sql);
            if ($updateStmt->execute($params)) {
                header("Location: index.php");
                exit;
            } else {
                $error = 'Có lỗi xảy ra khi cập nhật.';
            }
        }
    }
}
?>

<h1 class="<?= UI_H1 ?>">Sửa người dùng</h1>

<div class="<?= UI_CARD ?> max-w-md">
<form action="" method="POST" class="<?= UI_CARD_CONTENT ?> gap-4!">
    <?php if ($error): ?><div class="<?= UI_ALERT_ERROR ?>"><?= e($error) ?></div><?php endif; ?>
    <div class="<?= UI_FIELD ?>">
        <label class="<?= UI_LABEL ?>">Tên đăng nhập (username)</label>
        <input type="text" name="username" value="<?= e($user['username']) ?>" class="<?= UI_INPUT ?>" required>
    </div>
    <div class="<?= UI_FIELD ?>">
        <label class="<?= UI_LABEL ?>">Họ và tên (full_name)</label>
        <input type="text" name="full_name" value="<?= e($user['full_name'] ?? '') ?>" class="<?= UI_INPUT ?>">
    </div>
    <div class="<?= UI_FIELD ?>">
        <label class="<?= UI_LABEL ?>">Mật khẩu mới (bỏ trống nếu không đổi)</label>
        <input type="password" name="password" class="<?= UI_INPUT ?>">
    </div>
    <div class="<?= UI_FIELD ?>">
        <label class="<?= UI_LABEL ?>">Vai trò</label>
        <select name="role" class="<?= UI_SELECT ?>" required>
            <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User (Khách)</option>
            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin (Quản trị)</option>
        </select>
    </div>
    <div class="flex gap-3">
        <button type="submit" class="<?= ui_btn() ?>">Cập nhật</button>
        <a href="index.php" class="<?= ui_btn('outline') ?>">Hủy</a>
    </div>
</form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>