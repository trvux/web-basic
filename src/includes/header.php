<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/ui.php';
$cartCount = array_sum($_SESSION['cart'] ?? []);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <?php require __DIR__ . '/head.php'; ?>
  <title>Shop laptop</title>
</head>
<body class="min-h-screen">
<header class="sticky top-0 z-10 bg-primary text-primary-foreground shadow-sm">
  <nav class="mx-auto flex max-w-6xl items-center gap-1 px-4 py-2.5 text-sm font-medium">
    <a href="/index.php" class="mr-4 text-lg font-bold tracking-tight">LaptopShop</a>
    <a href="/index.php" class="rounded-md px-3 py-1.5 hover:bg-white/15">Trang chủ</a>
    <a href="/cart/index.php" class="rounded-md px-3 py-1.5 hover:bg-white/15">Giỏ hàng (<?= $cartCount ?>)</a>
    <?php if (!empty($_SESSION['user'])): ?>
      <a href="/orders/index.php" class="rounded-md px-3 py-1.5 hover:bg-white/15">Đơn hàng của tôi</a>
    <?php endif; ?>
    <?php if (is_admin()): ?>
      <a href="/admin/index.php" class="rounded-md px-3 py-1.5 hover:bg-white/15">Trang Admin</a>
    <?php endif; ?>

    <div class="ml-auto flex items-center gap-1">
      <?php if (!empty($_SESSION['user'])): ?>
        <span class="px-2 opacity-90">Xin chào, <?= e($_SESSION['user']['name'] ?? $_SESSION['user']['username']) ?></span>
        <a href="/auth/logout.php" class="rounded-md px-3 py-1.5 hover:bg-white/15">Đăng xuất</a>
      <?php else: ?>
        <a href="/auth/login.php" class="rounded-md px-3 py-1.5 hover:bg-white/15">Đăng nhập</a>
        <a href="/auth/register.php" class="rounded-md bg-white px-3 py-1.5 text-primary hover:bg-white/90">Đăng ký</a>
      <?php endif; ?>
    </div>
  </nav>
</header>
<main class="<?= UI_PAGE ?>">
