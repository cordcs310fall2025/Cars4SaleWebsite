<?php
// components/header.php
$config = require __DIR__ . '/../config.php';
$base = $config['base_url'];
$user = $_SESSION['user'] ?? null;
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>CarMart</title>
  <link rel="stylesheet" href="<?= $base ?>assets/css/style.css">
  <script src="<?= $base ?>assets/js/main.js" defer></script>
</head>
<body>
<header class="navbar">
  <div class="logo"><a href="<?= $base ?>">CarMart</a></div>
  <nav>
    <a href="<?= $base ?>?p=shop">Cars</a>
    <a href="<?= $base ?>?p=cart">Cart (<?= isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'],'qty')) : 0 ?>)</a>
    <?php if($user): ?>
      <span>Hi, <?= htmlspecialchars($user['name']) ?></span>
      <a href="<?= $base ?>?p=logout">Logout</a>
    <?php else: ?>
      <a href="<?= $base ?>?p=login">Login</a>
      <a href="<?= $base ?>?p=register">Register</a>
    <?php endif; ?>
  </nav>
</header>
<main>
