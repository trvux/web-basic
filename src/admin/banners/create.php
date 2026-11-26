<?php
require_once __DIR__ . '/../../includes/admin_header.php';
require_once __DIR__ . '/../../includes/upload.php';
require_once __DIR__ . '/save.php';

$banner = ['title' => '', 'image' => '', 'link' => '', 'sort_order' => 0, 'is_active' => 1];
$error = '';

// CREATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error = save_banner($pdo, $banner);
    if ($error === null) {
        header('Location: index.php');
        exit;
    }
}
?>

<h1 class="<?= UI_H1 ?>">Thêm banner</h1>
<?php require __DIR__ . '/form.php'; ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
