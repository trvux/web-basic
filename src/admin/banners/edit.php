<?php
require_once __DIR__ . '/../../includes/admin_header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Lấy dữ liệu banner cũ
$stmt = $pdo->prepare('SELECT * FROM banners WHERE id = ?');
$stmt->execute([$id]);
$banner = $stmt->fetch();

if (!$banner) {
    die('Không tìm thấy banner!');
}

// Xử lý khi bấm nút Cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $image = $_POST['image'] ?? '';
    $status = isset($_POST['status']) ? (int)$_POST['status'] : 0;

    $update = $pdo->prepare('UPDATE banners SET title = ?, image = ?, status = ? WHERE id = ?');
    $update->execute([$title, $image, $status, $id]);
    
    header('Location: index.php');
    exit;
}
?>

<div class="mb-5 flex items-center justify-between">
  <h1 class="text-2xl font-semibold tracking-tight">Sửa banner</h1>
  <a href="index.php" class="<?= ui_btn('outline') ?>">Quay lại</a>
</div>

<form method="post" class="max-w-xl space-y-4 bg-white p-6 rounded-lg shadow">
  <div>
    <label class="block text-sm font-medium mb-1 text-gray-700">Tiêu đề</label>
    <input type="text" name="title" value="<?= e($banner['title']) ?>" class="w-full border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
  </div>
  
  <div>
    <label class="block text-sm font-medium mb-1 text-gray-700">Đường dẫn hình ảnh (URL)</label>
    <input type="text" name="image" value="<?= e($banner['image']) ?>" class="w-full border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="https://example.com/anh-banner.jpg">
  </div>

  <div>
    <label class="block text-sm font-medium mb-1 text-gray-700">Trạng thái</label>
    <select name="status" class="w-full border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
      <option value="1" <?= $banner['status'] == 1 ? 'selected' : '' ?>>Hiển thị</option>
      <option value="0" <?= $banner['status'] == 0 ? 'selected' : '' ?>>Đang ẩn</option>
    </select>
  </div>

  <div class="pt-4">
    <button type="submit" class="<?= ui_btn() ?>">Cập nhật banner</button>
  </div>
</form>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>