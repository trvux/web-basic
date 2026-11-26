<?php
require_once __DIR__ . '/../../includes/admin_header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare('SELECT * FROM posts WHERE id = ?');
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) { die('Không tìm thấy bài viết!'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $image = $_POST['image'] ?? '';
    $status = isset($_POST['status']) ? (int)$_POST['status'] : 0;

    $update = $pdo->prepare('UPDATE posts SET title = ?, content = ?, image = ?, status = ? WHERE id = ?');
    $update->execute([$title, $content, $image, $status, $id]);
    
    header('Location: index.php');
    exit;
}
?>

<div class="mb-5 flex items-center justify-between">
  <h1 class="text-2xl font-semibold tracking-tight">Sửa bài viết</h1>
  <a href="index.php" class="<?= ui_btn('outline') ?>">Quay lại</a>
</div>

<form method="post" class="max-w-2xl space-y-4 bg-white p-6 rounded-lg shadow">
  <div>
    <label class="block text-sm font-medium mb-1 text-gray-700">Tiêu đề</label>
    <input type="text" name="title" value="<?= e($post['title']) ?>" class="w-full border border-gray-300 rounded p-2" required>
  </div>
  <div>
    <label class="block text-sm font-medium mb-1 text-gray-700">Hình ảnh (URL)</label>
    <input type="text" name="image" value="<?= e($post['image']) ?>" class="w-full border border-gray-300 rounded p-2">
  </div>
  <div>
    <label class="block text-sm font-medium mb-1 text-gray-700">Nội dung</label>
    <textarea name="content" rows="5" class="w-full border border-gray-300 rounded p-2"><?= e($post['content']) ?></textarea>
  </div>
  <div>
    <label class="block text-sm font-medium mb-1 text-gray-700">Trạng thái</label>
    <select name="status" class="w-full border border-gray-300 rounded p-2">
      <option value="1" <?= $post['status'] == 1 ? 'selected' : '' ?>>Hiển thị</option>
      <option value="0" <?= $post['status'] == 0 ? 'selected' : '' ?>>Đang ẩn</option>
    </select>
  </div>
  <div class="pt-4">
    <button type="submit" class="<?= ui_btn() ?>">Cập nhật bài viết</button>
  </div>
</form>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>