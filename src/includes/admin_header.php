<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/ui.php';
require_admin(); // chặn người không phải admin (xem config/db.php)
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <?php require __DIR__ . '/head.php'; ?>
  <title>Admin - LaptopShop</title>
</head>
<body class="min-h-screen">
<header class="bg-foreground text-background">
  <nav class="mx-auto flex max-w-6xl items-center gap-1 px-4 py-2.5 text-sm font-medium">
    <span class="mr-4 rounded-md bg-primary px-2 py-0.5 text-xs font-bold tracking-wider text-primary-foreground">ADMIN</span>
    <a href="/admin/index.php" class="rounded-md px-3 py-1.5 hover:bg-white/15">Dashboard</a>
    <a href="/admin/products/index.php" class="rounded-md px-3 py-1.5 hover:bg-white/15">Sản phẩm</a>
    <a href="/admin/categories/index.php" class="rounded-md px-3 py-1.5 hover:bg-white/15">Danh mục</a>
    <a href="/admin/orders/index.php" class="rounded-md px-3 py-1.5 hover:bg-white/15">Đơn hàng</a>
    <div class="ml-auto flex items-center gap-1">
      <a href="/index.php" class="rounded-md px-3 py-1.5 hover:bg-white/15">Xem trang bán hàng</a>
      <a href="/auth/logout.php" class="rounded-md px-3 py-1.5 hover:bg-white/15">Đăng xuất</a>
    </div>
  </nav>
</header>
<main class="<?= UI_PAGE ?>">
