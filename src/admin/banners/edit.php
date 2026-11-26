<?php
require_once __DIR__ . '/../../includes/admin_header.php';
require_once __DIR__ . '/../../includes/upload.php';
require_once __DIR__ . '/save.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM banners WHERE id = ?');
$stmt->execute([$id]);
$banner = $stmt->fetch();

if (!$banner) {
    echo '<div class="' . UI_EMPTY . '">Không tìm thấy banner.</div>';
    require __DIR__ . '/../../includes/footer.php';
    exit;
}

$error = '';

// UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error = save_banner($pdo, $banner, $id);
    if ($error === null) {
        header('Location: index.php');
        exit;
    }
}
?>

<h1 class="<?= UI_H1 ?>">Sửa banner #<?= $id ?></h1>
<?php require __DIR__ . '/form.php'; ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
