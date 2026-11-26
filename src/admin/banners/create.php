<?php
require_once __DIR__ . '/../../includes/admin_header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $image = $_POST['image'] ?? ''; // Dùng cột image
    $status = isset($_POST['status']) ? (int)$_POST['status'] : 0;

    // Chèn vào database (cột image)
    $stmt = $pdo->prepare('INSERT INTO banners (title, image, status) VALUES (?, ?, ?)');
    $stmt->execute([$title, $image, $status]);
    
    // Quay về trang danh sách
    header('Location: index.php');
    exit;
}
?>

<div class="mb-5 flex items-center justify-between">
  <h1 class="text-2xl font-semibold tracking-tight">Thêm banner mới</h1>
  <a href="index.php" class="<?= ui_btn('outline') ?>">Quay lại</a>
</div>

<form method="post" class="max-w-xl space-y-4 bg-white p-6 rounded-lg shadow">
  <div>
    <label class="block text-sm font-medium mb-1 text-gray-700">Tiêu đề</label>
    <input type="text" name="title" class="w-full border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
  </div>
  
  <div>
    <label class="block text-sm font-medium mb-1 text-gray-700">Đường dẫn hình ảnh (URL)</label>
    <!-- Dùng name="image" -->
    <input type="text" name="image" class="w-full border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="https://example.com/anh-banner.jpg">
  </div>

  <div>
    <label class="block text-sm font-medium mb-1 text-gray-700">Trạng thái</label>
    <select name="status" class="w-full border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
      <option value="1">Hiển thị</option>
      <option value="0">Đang ẩn</option>
    </select>
  </div>

  <div class="pt-4">
    <button type="submit" class="<?= ui_btn() ?>">Lưu banner</button>
  </div>
</form>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>