<?php
session_start();
require 'db.php';

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<h2>Your cart is empty.</h2>";
    exit;
}

$ids = implode(',', array_keys($_SESSION['cart']));
$result = $conn->query("SELECT * FROM cars WHERE car_id IN ($ids)");

$total = 0;
?>

<h2>My Cart</h2>

<form method="post" action="update_cart.php">
<table border="1" cellpadding="10" width="100%">
<tr>
    <th>Car</th>
    <th>Price</th>
    <th>Quantity</th>
    <th>Subtotal</th>
    <th>Remove</th>
</tr>

<?php while ($car = $result->fetch_assoc()):
    $qty = $_SESSION['cart'][$car['car_id']];
    $subtotal = $car['price'] * $qty;
    $total += $subtotal;
?>
<tr>
    <td><?= htmlspecialchars($car['model']) ?></td>
    <td>$<?= number_format($car['price'], 2) ?></td>
    <td>
        <input type="number" name="qty[<?= $car['car_id'] ?>]" value="<?= $qty ?>" min="1">
    </td>
    <td>$<?= number_format($subtotal, 2) ?></td>
    <td>
        <a href="remove_from_cart.php?car_id=<?= $car['car_id'] ?>">❌</a>
    </td>
</tr>
<?php endwhile; ?>

<tr>
    <td colspan="3"><strong>Total</strong></td>
    <td colspan="2"><strong>$<?= number_format($total, 2) ?></strong></td>
</tr>
</table>

<br>
<button type="submit">Update Cart</button>
<a href="checkout.php"><button type="button">Proceed to Checkout</button></a>
</form>
