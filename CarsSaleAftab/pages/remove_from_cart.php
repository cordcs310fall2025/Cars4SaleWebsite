<?php
session_start();

$car_id = intval($_GET['car_id']);

unset($_SESSION['cart'][$car_id]);

header("Location: cart.php");
exit;
<?php
session_start();
$id = $_GET['id'];

if (($key = array_search($id, $_SESSION['cart'])) !== false) {
    unset($_SESSION['cart'][$key]);
}

header("Location: cart.php");
exit;
