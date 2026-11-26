<?php
require_once __DIR__ . '/../../includes/admin_header.php';
require_once __DIR__ . '/../../includes/upload.php';
require_once __DIR__ . '/save.php';

$post = ['title' => '', 'content' => '', 'image' => ''];
$error = '';

// CREATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error = save_post($pdo, $post);
    if ($error === null) {
        header('Location: index.php');
        exit;
    }
}
?>

<h1 class="<?= UI_H1 ?>">Thêm bài viết</h1>
<?php require __DIR__ . '/form.php'; ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
