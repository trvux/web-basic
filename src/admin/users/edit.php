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
    die('Người dùng không tồn tại.');
}

$current_user_id = $_SESSION['user_id'] ?? 0; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $role = $_POST['role'] ?? 'user';
    $password = $_POST['password'] ?? '';

    if ($id === $current_user_id && $role !== 'admin') {
        $error = 'Bạn không thể tự hạ quyền của chính mình!';
    } else {
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET full_name = ?, role = ?, password = ? WHERE id = ?";
            $params = [$full_name, $role, $hashed_password, $id];
        } else {
            $sql = "UPDATE users SET full_name = ?, role = ? WHERE id = ?";
            $params = [$full_name, $role, $id];
        }

        $updateStmt = $pdo->prepare($sql);
        if ($updateStmt->execute($params)) {
            header("Location: index.php");
            exit;
        } else {
            $error = 'Có lỗi xảy ra khi cập nhật.';
        }
    }
}
?>

<div class="p-6 max-w-lg mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Sửa người dùng</h1>

    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?= e($error) ?></div>
    <?php endif; ?>

    <form action="" method="POST" class="<?= UI_CARD ?> p-6">
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Tên đăng nhập (Không thể sửa)</label>
            <input type="text" value="<?= e($user['username']) ?>" class="<?= UI_INPUT ?> w-full bg-gray-100" readonly>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Mật khẩu mới (Bỏ trống nếu không đổi)</label>
            <input type="password" name="password" class="<?= UI_INPUT ?> w-full" placeholder="Nhập mật khẩu mới...">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Họ và tên</label>
            <input type="text" name="full_name" value="<?= e($user['full_name']) ?>" class="<?= UI_INPUT ?> w-full">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Vai trò</label>
            <select name="role" class="<?= UI_INPUT ?> w-full">
                <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>Khách hàng (user)</option>
                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Quản trị viên (admin)</option>
            </select>
        </div>
        <div class="flex gap-3">
            <!-- Sửa nút: Dùng class tĩnh -->
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">Cập nhật</button>
            <a href="index.php" class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300 transition">Hủy</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>