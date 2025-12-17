<?php
session_start();

$car_id = intval($_GET['car_id']);

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_SESSION['cart'][$car_id])) {
    $_SESSION['cart'][$car_id]++;
} else {
    $_SESSION['cart'][$car_id] = 1;
}

header("Location: cart.php");
exit;
<?php
require "includes/db.php";

$car_id = $_POST['car_id'];

$_SESSION['cart'][] = $car_id;

header("Location: cart.php");
exit;
<?php
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (!isset($_GET['car_id'])) {
    header("Location: home.php");
    exit;
}

$car_id = (int)$_GET['car_id'];

// Quantity-based cart
if (isset($_SESSION['cart'][$car_id])) {
    $_SESSION['cart'][$car_id]++;
} else {
    $_SESSION['cart'][$car_id] = 1;
}

header("Location: cart.php");
exit;
<a class="btn cart"
   href="/CarsSaleAftab/add_cart.php?car_id=<?= $cheapest_id ?>">
   Add to Cart
</a>
