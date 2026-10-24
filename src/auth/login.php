<?php
require_once __DIR__ . '/../includes/header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // password_verify so mật khẩu nhập vào với hash trong DB
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role'],
        ];
        // admin vào dashboard, khách về trang chủ
        header('Location: ' . ($user['role'] === 'admin' ? '/admin/index.php' : '/index.php'));
        exit;
    }
    $error = 'Sai tên đăng nhập hoặc mật khẩu';
}
?>

<div class="mx-auto max-w-sm">
  <div class="<?= UI_CARD ?>">
    <div class="<?= UI_CARD_HEADER ?>">
      <h1 class="text-xl font-semibold">Đăng nhập</h1>
      <p class="<?= UI_CARD_DESC ?>">Nhập tài khoản để tiếp tục</p>
    </div>
    <form method="post" class="<?= UI_CARD_CONTENT ?> gap-4!">
      <?php if ($error): ?><div class="<?= UI_ALERT_ERROR ?>"><?= e($error) ?></div><?php endif; ?>
      <div class="<?= UI_FIELD ?>">
        <label class="<?= UI_LABEL ?>" for="username">Tên đăng nhập</label>
        <input class="<?= UI_INPUT ?>" id="username" type="text" name="username" required>
      </div>
      <div class="<?= UI_FIELD ?>">
        <label class="<?= UI_LABEL ?>" for="password">Mật khẩu</label>
        <input class="<?= UI_INPUT ?>" id="password" type="password" name="password" required>
      </div>
      <button type="submit" class="<?= ui_btn() ?>">Đăng nhập</button>
      <p class="text-center text-sm text-muted-foreground">
        Chưa có tài khoản? <a href="register.php" class="text-link underline underline-offset-4">Đăng ký</a>
      </p>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
