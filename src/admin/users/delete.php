<?php
require_once __DIR__ . '/../../config/db.php';
require_admin(); 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $current_user_id = (int)($_SESSION['user']['id'] ?? 0);
    
    if ($id === $current_user_id) {
        header("Location: index.php");
        exit;
    }
    
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: index.php");
exit;