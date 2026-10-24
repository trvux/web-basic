<?php
require_once __DIR__ . '/../includes/header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    if ($username === '' || strlen($password) < 6) {
        $error = 'Tên đăng nhập không được trống và mật khẩu tối thiểu 6 ký tự';
    } elseif ($password !== $confirm) {
        $error = 'Mật khẩu nhập lại không khớp';
    } else {
        // kiểm tra trùng username
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = 'Tên đăng nhập đã tồn tại';
        } else {
            // Mã hoá mật khẩu trước khi lưu, KHÔNG lưu mật khẩu thô
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
            $stmt->execute([$username, $hash]);
            header('Location: login.php');
            exit;
        }
    }
}
?>

<div class="mx-auto max-w-sm">
  <div class="<?= UI_CARD ?>">
    <div class="<?= UI_CARD_HEADER ?>">
      <h1 class="text-xl font-semibold">Đăng ký</h1>
      <p class="<?= UI_CARD_DESC ?>">Tạo tài khoản mới để mua hàng</p>
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
      <div class="<?= UI_FIELD ?>">
        <label class="<?= UI_LABEL ?>" for="confirm">Nhập lại mật khẩu</label>
        <input class="<?= UI_INPUT ?>" id="confirm" type="password" name="confirm" required>
      </div>
      <button type="submit" class="<?= ui_btn() ?>">Đăng ký</button>
      <p class="text-center text-sm text-muted-foreground">
        Đã có tài khoản? <a href="login.php" class="text-link underline underline-offset-4">Đăng nhập</a>
      </p>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
