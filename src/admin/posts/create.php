<?php
require_once __DIR__ . '/../../includes/admin_header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $image = $_POST['image'] ?? '';
    $status = isset($_POST['status']) ? (int)$_POST['status'] : 0;

    $stmt = $pdo->prepare('INSERT INTO posts (title, content, image, status) VALUES (?, ?, ?, ?)');
    $stmt->execute([$title, $content, $image, $status]);
    
    header('Location: index.php');
    exit;
}
?>

<div class="mb-5 flex items-center justify-between">
  <h1 class="text-2xl font-semibold tracking-tight">Thêm bài viết mới</h1>
  <a href="index.php" class="<?= ui_btn('outline') ?>">Quay lại</a>
</div>

<form method="post" class="max-w-2xl space-y-4 bg-white p-6 rounded-lg shadow">
  <div>
    <label class="block text-sm font-medium mb-1 text-gray-700">Tiêu đề</label>
    <input type="text" name="title" class="w-full border border-gray-300 rounded p-2" required>
  </div>
  <div>
    <label class="block text-sm font-medium mb-1 text-gray-700">Hình ảnh (URL)</label>
    <input type="text" name="image" class="w-full border border-gray-300 rounded p-2" placeholder="https://example.com/anh.jpg">
  </div>
  <div>
    <label class="block text-sm font-medium mb-1 text-gray-700">Nội dung</label>
    <textarea name="content" rows="5" class="w-full border border-gray-300 rounded p-2"></textarea>
  </div>
  <div>
    <label class="block text-sm font-medium mb-1 text-gray-700">Trạng thái</label>
    <select name="status" class="w-full border border-gray-300 rounded p-2">
      <option value="1">Hiển thị</option>
      <option value="0">Đang ẩn</option>
    </select>
  </div>
  <div class="pt-4">
    <button type="submit" class="<?= ui_btn() ?>">Lưu bài viết</button>
  </div>
</form>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>