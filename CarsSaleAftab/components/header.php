<!-- components/header.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarMart - Buy Your Next Car</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <script src="/assets/js/main.js" defer></script>
</head>
<body>

<header class="navbar">
    <div class="logo">CarMart</div>

    <nav>
        <a href="/pages/home.php">Home</a>
        <a href="/pages/shop.php">Cars</a>
        <a href="/pages/cart.php">Cart</a>
        <a href="/pages/login.php">Login</a>
    </nav>
</header>

<main>
    <?php
$cart_count = isset($_SESSION['cart']) 
    ? array_sum($_SESSION['cart']) 
    : 0;
?>

<a href="cart.php" style="position:relative;">
    🛒 Cart
    <?php if ($cart_count > 0): ?>
        <span style="
            position:absolute;
            top:-8px;
            right:-10px;
            background:red;
            color:white;
            border-radius:50%;
            padding:3px 7px;
            font-size:12px;">
            <?= $cart_count ?>
        </span>
    <?php endif; ?>
</a>

