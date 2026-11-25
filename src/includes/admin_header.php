<?php
// Khung trang admin theo shadcn sidebar: sidebar bên trái (thu gọn được), nội dung bên phải.
// Điện thoại: sidebar thành ngăn trượt. Mọi trang admin chỉ cần include file này và footer.php.
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/ui.php';
require_once __DIR__ . '/icons.php';
require_admin(); // chặn người không phải admin (xem config/db.php)
$isAdminArea = true; // footer.php sẽ đóng khung sidebar thay vì in chân trang cửa hàng

// nhóm => [nhãn => [đường dẫn, icon]]
$adminNav = [
    'Tổng quan' => [
        'Dashboard' => ['/admin/index.php', 'dashboard'],
    ],
    'Cửa hàng' => [
        'Sản phẩm' => ['/admin/products/index.php', 'package'],
        'Danh mục' => ['/admin/categories/index.php', 'folder'],
        'Đơn hàng' => ['/admin/orders/index.php', 'cart'],
        'Người dùng' => ['/admin/users/index.php', 'users'],
    ],
    'Nội dung' => [
        'Banner' => ['/admin/banners/index.php', 'image'],
        'Bài viết' => ['/admin/posts/index.php', 'file'],
    ],
    'Phát triển' => [
        'Component' => ['/admin/components.php', 'components'],
    ],
];

// Mục đang mở: trang trong /admin/ thì so đúng file, trang trong thư mục con thì so theo thư mục
$current = $_SERVER['SCRIPT_NAME'];
$isActive = fn($href) => dirname($href) === '/admin' ? $current === $href : dirname($href) === dirname($current);

$pageTitle = 'Quản trị';
foreach ($adminNav as $items) {
    foreach ($items as $label => [$href]) {
        if ($isActive($href)) $pageTitle = $label;
    }
}
$userName = $_SESSION['user']['name'] ?? $_SESSION['user']['username'] ?? 'admin';

// Nội dung sidebar, dùng chung cho máy tính và ngăn trượt điện thoại
function admin_sidebar($adminNav, $isActive, $userName) {
    // nhãn bị ẩn khi sidebar thu gọn
    $hide = 'group-data-[collapsed]/sidebar:hidden';
    $item = 'flex h-8 w-full items-center gap-2 overflow-hidden rounded-md px-2 text-sm whitespace-nowrap outline-none hover:bg-sidebar-accent hover:text-sidebar-accent-foreground focus-visible:ring-2 focus-visible:ring-sidebar-ring';
    ?>
    <a href="/admin/index.php" class="flex h-14 shrink-0 items-center gap-2 border-b border-sidebar-border px-2">
      <span class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-primary text-primary-foreground"><?= ui_icon('store') ?></span>
      <span class="grid leading-tight <?= $hide ?>">
        <span class="text-sm font-semibold">LaptopShop</span>
        <span class="text-xs text-muted-foreground">Trang quản trị</span>
      </span>
    </a>
    <nav class="flex min-h-0 flex-1 flex-col overflow-y-auto">
      <?php foreach ($adminNav as $group => $items): ?>
        <div class="flex flex-col gap-1 p-2">
          <div class="flex h-6 items-center px-2 text-xs font-medium text-sidebar-foreground/70 <?= $hide ?>"><?= e($group) ?></div>
          <?php foreach ($items as $label => [$href, $icon]): ?>
            <a href="<?= e($href) ?>" title="<?= e($label) ?>" <?= $isActive($href) ? 'aria-current="page"' : '' ?>
               class="<?= $item ?> <?= $isActive($href) ? 'bg-sidebar-accent font-medium text-sidebar-accent-foreground' : '' ?>">
              <?= ui_icon($icon) ?><span class="truncate <?= $hide ?>"><?= e($label) ?></span>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>
    </nav>
    <div class="flex flex-col gap-1 border-t border-sidebar-border p-2">
      <div class="flex h-10 items-center gap-2 overflow-hidden">
        <span class="<?= UI_AVATAR ?>"><span class="<?= UI_AVATAR_FALLBACK ?>"><?= e(strtoupper(preg_match("/^./u", $userName, $first) ? $first[0] : "A")) ?></span></span>
        <span class="truncate text-sm font-medium <?= $hide ?>"><?= e($userName) ?></span>
      </div>
      <a href="/index.php" title="Xem trang bán hàng" class="<?= $item ?>"><?= ui_icon('store') ?><span class="<?= $hide ?>">Xem trang bán hàng</span></a>
      <a href="/auth/logout.php" title="Đăng xuất" class="<?= $item ?>"><?= ui_icon('logout') ?><span class="<?= $hide ?>">Đăng xuất</span></a>
    </div>
    <?php
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <?php require __DIR__ . '/head.php'; ?>
  <title><?= e($pageTitle) ?> - Admin LaptopShop</title>
</head>
<body class="min-h-svh">
<div class="flex min-h-svh w-full">
  <aside id="admin-sidebar" class="group/sidebar sticky top-0 hidden h-svh w-64 shrink-0 flex-col border-r border-sidebar-border bg-sidebar text-sidebar-foreground transition-[width] duration-200 md:flex data-[collapsed]:w-12.5">
    <?php admin_sidebar($adminNav, $isActive, $userName); ?>
  </aside>
  <script>
    // Nhớ trạng thái thu gọn, đặt ngay để không bị nháy khi tải trang
    if (localStorage.getItem('sidebar') === 'collapsed') document.getElementById('admin-sidebar').toggleAttribute('data-collapsed', true);
  </script>

  <dialog id="mobile-menu" class="<?= UI_SHEET_LEFT ?> w-72! gap-0! bg-sidebar! p-0! text-sidebar-foreground!">
    <?php admin_sidebar($adminNav, $isActive, $userName); ?>
  </dialog>

  <div class="flex min-w-0 flex-1 flex-col">
    <header class="sticky top-0 z-10 flex h-14 shrink-0 items-center gap-2 border-b bg-background px-4">
      <button type="button" data-sidebar-toggle class="<?= ui_btn('ghost', 'sm') ?> hidden! md:inline-flex!" aria-label="Thu gọn sidebar"><?= ui_icon('panel') ?></button>
      <button type="button" data-open="mobile-menu" class="<?= ui_btn('ghost', 'sm') ?> md:hidden!" aria-label="Mở menu"><?= ui_icon('panel') ?></button>
      <span class="h-4 w-px bg-border" aria-hidden="true"></span>
      <?= ui_breadcrumb(['Admin' => '/admin/index.php'], $pageTitle, '') ?>
    </header>
    <main class="mx-auto w-full max-w-6xl flex-1 p-4 md:p-6">
