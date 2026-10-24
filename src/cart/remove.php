<?php
require_once __DIR__ . '/../config/db.php';

unset($_SESSION['cart'][(int)($_GET['id'] ?? 0)]);

header('Location: index.php');
exit;
