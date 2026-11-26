<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/upload.php';
require_admin();

// DELETE: chỉ nhận POST, xoá cả file ảnh
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $stmt = $pdo->prepare('SELECT image FROM banners WHERE id = ?');
    $stmt->execute([$id]);
    $image = $stmt->fetchColumn();

    $pdo->prepare('DELETE FROM banners WHERE id = ?')->execute([$id]);
    delete_image_upload($image, 'banners');
}

header('Location: index.php');
exit;
