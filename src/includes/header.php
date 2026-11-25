<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/ui.php';
$cartCount = array_sum($_SESSION['cart'] ?? []);

// Menu dùng chung cho máy tính (hàng ngang) và điện thoại (ngăn trượt)
$nav = [
    'Trang chủ' => '/index.php',
    'Sản phẩm' => '/products/index.php',
    'Tin tức' => '/posts/index.php',
    "Giỏ hàng ($cartCount)" => '/cart/index.php',
];
if (!empty($_SESSION['user'])) $nav['Đơn hàng của tôi'] = '/orders/index.php';
if (is_admin()) $nav['Trang Admin'] = '/admin/index.php';
$userName = $_SESSION['user']['name'] ?? $_SESSION['user']['username'] ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <?php require __DIR__ . '/head.php'; ?>
  <title>LaptopShop</title>
</head>
<body class="flex min-h-screen flex-col">
<header class="sticky top-0 z-10 bg-primary text-primary-foreground shadow-sm">
  <nav class="mx-auto flex max-w-6xl items-center gap-1 px-4 py-2.5 text-sm font-medium">
    <a href="/index.php" class="mr-4 text-lg font-bold tracking-tight">LaptopShop</a>
    <div class="hidden items-center gap-1 lg:flex">
      <?php foreach ($nav as $label => $href): ?>
        <a href="<?= e($href) ?>" class="rounded-md px-3 py-1.5 hover:bg-white/15"><?= e($label) ?></a>
      <?php endforeach; ?>
    </div>

    <div class="ml-auto hidden items-center gap-1 lg:flex">
      <?php if (!empty($_SESSION['user'])): ?>
        <span class="px-2 opacity-90">Xin chào, <?= e($userName) ?></span>
        <a href="/auth/logout.php" class="rounded-md px-3 py-1.5 hover:bg-white/15">Đăng xuất</a>
      <?php else: ?>
        <a href="/auth/login.php" class="rounded-md px-3 py-1.5 hover:bg-white/15">Đăng nhập</a>
        <a href="/auth/register.php" class="rounded-md bg-white px-3 py-1.5 text-primary hover:bg-white/90">Đăng ký</a>
      <?php endif; ?>
    </div>

    <a href="/cart/index.php" class="ml-auto rounded-md px-3 py-1.5 hover:bg-white/15 lg:hidden">Giỏ (<?= $cartCount ?>)</a>
    <button type="button" data-open="mobile-menu" class="cursor-pointer rounded-md px-3 py-1.5 text-lg hover:bg-white/15 lg:hidden" aria-label="Mở menu">☰</button>
  </nav>
</header>

<dialog id="mobile-menu" class="<?= UI_SHEET_RIGHT ?>">
  <form method="dialog" class="flex items-center justify-between">
    <span class="<?= UI_DIALOG_TITLE ?>"><?= $userName !== '' ? 'Xin chào, ' . e($userName) : 'Menu' ?></span>
    <button class="<?= ui_btn('ghost', 'sm') ?>" aria-label="Đóng menu">✕</button>
  </form>
  <div class="flex flex-col gap-1">
    <?php foreach ($nav as $label => $href): ?>
      <a href="<?= e($href) ?>" class="<?= ui_sidebar_link() ?>"><?= e($label) ?></a>
    <?php endforeach; ?>
  </div>
  <div class="<?= UI_SHEET_FOOTER ?>">
    <?php if (!empty($_SESSION['user'])): ?>
      <a href="/auth/logout.php" class="<?= ui_btn('outline') ?>">Đăng xuất</a>
    <?php else: ?>
      <a href="/auth/login.php" class="<?= ui_btn('outline') ?>">Đăng nhập</a>
      <a href="/auth/register.php" class="<?= ui_btn() ?>">Đăng ký</a>
    <?php endif; ?>
  </div>
</dialog>
<main class="<?= UI_PAGE ?> flex-1">
