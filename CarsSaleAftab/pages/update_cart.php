<?php
session_start();

foreach ($_POST['qty'] as $car_id => $quantity) {
    if ($quantity <= 0) {
        unset($_SESSION['cart'][$car_id]);
    } else {
        $_SESSION['cart'][$car_id] = intval($quantity);
    }
}

header("Location: cart.php");
exit;
$_SESSION['cart'][$car_id] = ($_SESSION['cart'][$car_id] ?? 0) + 1;
<p>Qty: <?= $_SESSION['cart'][$id] ?></p>
