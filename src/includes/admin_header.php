<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/ui.php';
require_admin(); // chặn người không phải admin (xem config/db.php)

$nav = [
    'Dashboard' => '/admin/index.php',
    'Sản phẩm' => '/admin/products/index.php',
    'Danh mục' => '/admin/categories/index.php',
    'Đơn hàng' => '/admin/orders/index.php',
    'Người dùng' => '/admin/users/index.php',
    'Banner' => '/admin/banners/index.php',
    'Bài viết' => '/admin/posts/index.php',
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <?php require __DIR__ . '/head.php'; ?>
  <title>Admin - LaptopShop</title>
</head>
<body class="flex min-h-screen flex-col">
<header class="bg-foreground text-background">
  <nav class="mx-auto flex max-w-6xl items-center gap-1 px-4 py-2.5 text-sm font-medium">
    <span class="mr-4 rounded-md bg-primary px-2 py-0.5 text-xs font-bold tracking-wider text-primary-foreground">ADMIN</span>
    <div class="hidden items-center gap-1 lg:flex">
      <?php foreach ($nav as $label => $href): ?>
        <a href="<?= e($href) ?>" class="rounded-md px-3 py-1.5 hover:bg-white/15"><?= e($label) ?></a>
      <?php endforeach; ?>
    </div>
    <div class="ml-auto hidden items-center gap-1 lg:flex">
      <a href="/index.php" class="rounded-md px-3 py-1.5 hover:bg-white/15">Xem trang bán hàng</a>
      <a href="/auth/logout.php" class="rounded-md px-3 py-1.5 hover:bg-white/15">Đăng xuất</a>
    </div>
    <button type="button" data-open="mobile-menu" class="ml-auto cursor-pointer rounded-md px-3 py-1.5 text-lg hover:bg-white/15 lg:hidden" aria-label="Mở menu">☰</button>
  </nav>
</header>

<dialog id="mobile-menu" class="<?= UI_SHEET_RIGHT ?>">
  <form method="dialog" class="flex items-center justify-between">
    <span class="<?= UI_DIALOG_TITLE ?>">Quản trị</span>
    <button class="<?= ui_btn('ghost', 'sm') ?>" aria-label="Đóng menu">✕</button>
  </form>
  <div class="flex flex-col gap-1">
    <?php foreach ($nav as $label => $href): ?>
      <a href="<?= e($href) ?>" class="<?= ui_sidebar_link() ?>"><?= e($label) ?></a>
    <?php endforeach; ?>
  </div>
  <div class="<?= UI_SHEET_FOOTER ?>">
    <a href="/index.php" class="<?= ui_btn('outline') ?>">Xem trang bán hàng</a>
    <a href="/auth/logout.php" class="<?= ui_btn('outline') ?>">Đăng xuất</a>
  </div>
</dialog>
<main class="<?= UI_PAGE ?> flex-1">
