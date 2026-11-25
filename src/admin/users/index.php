<?php
// TRANG TẠM do leader tạo để menu không bị 404. Phong thay toàn bộ nội dung file này bằng trang thật.
// Mẫu để copy: src/admin/products/ (admin) hoặc src/index.php (khách). Dùng ui.php, không viết màu Tailwind cứng.
require_once __DIR__ . '/../../includes/admin_header.php';
?>

<h1 class="<?= UI_H1 ?>">Quản lý người dùng</h1>
<div class="<?= UI_EMPTY ?>">Đang xây dựng.</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
