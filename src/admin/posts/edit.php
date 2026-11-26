<?php
require_once __DIR__ . '/../../includes/admin_header.php';
require_once __DIR__ . '/../../includes/upload.php';
require_once __DIR__ . '/save.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM posts WHERE id = ?');
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) {
    echo '<div class="' . UI_EMPTY . '">Không tìm thấy bài viết.</div>';
    require __DIR__ . '/../../includes/footer.php';
    exit;
}

$error = '';

// UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error = save_post($pdo, $post, $id);
    if ($error === null) {
        header('Location: index.php');
        exit;
    }
}
?>

<h1 class="<?= UI_H1 ?>">Sửa bài viết #<?= $id ?></h1>
<?php require __DIR__ . '/form.php'; ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
