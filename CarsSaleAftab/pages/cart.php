<?php
// pages/cart.php
// handle add/remove actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $car_id = intval($_POST['car_id']);
        $qty = max(1,intval($_POST['qty'] ?? 1));
        // get car from DB
        $stmt = $pdo->prepare("SELECT id,make,model,price,image FROM cars WHERE id = ?");
        $stmt->execute([$car_id]);
        $car = $stmt->fetch();
        if ($car) {
            if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
            if (isset($_SESSION['cart'][$car_id])) {
                $_SESSION['cart'][$car_id]['qty'] += $qty;
            } else {
                $_SESSION['cart'][$car_id] = [
                  'id' => $car['id'],
                  'title' => $car['make'].' '.$car['model'],
                  'price' => $car['price'],
                  'image' => $car['image'],
                  'qty' => $qty
                ];
            }
        }
    } elseif ($action === 'remove') {
        $car_id = intval($_POST['car_id']);
        if (isset($_SESSION['cart'][$car_id])) {
            unset($_SESSION['cart'][$car_id]);
        }
    } elseif ($action === 'update') {
        foreach ($_POST['qty'] as $id => $q) {
            $id = intval($id);
            $q = max(1,intval($q));
            if (isset($_SESSION['cart'][$id])) {
                $_SESSION['cart'][$id]['qty'] = $q;
            }
        }
    }
    // redirect to avoid form resubmission
    header("Location: /?p=cart");
    exit;
}

// display cart
$cart = $_SESSION['cart'] ?? [];
$total = 0;
foreach ($cart as $item) { $total += $item['price'] * $item['qty']; }
?>
<h1>Your Cart</h1>
<?php if (!$cart): ?>
  <p>Your cart is empty. <a href="/?p=shop">Browse cars</a></p>
<?php else: ?>
  <form method="POST" action="?p=cart">
    <input type="hidden" name="action" value="update">
    <table>
      <tr><th>Car</th><th>Price</th><th>Qty</th><th>Subtotal</th><th></th></tr>
      <?php foreach ($cart as $it): ?>
      <tr>
        <td><?= htmlspecialchars($it['title']) ?></td>
        <td>$<?= number_format($it['price'],2) ?></td>
        <td><input type="number" name="qty[<?= $it['id'] ?>]" value="<?= $it['qty'] ?>" min="1"></td>
        <td>$<?= number_format($it['price'] * $it['qty'],2) ?></td>
        <td>
          <form method="POST" action="?p=cart" style="display:inline">
            <input type="hidden" name="action" value="remove">
            <input type="hidden" name="car_id" value="<?= $it['id'] ?>">
            <button>Remove</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </table>
    <button>Update quantities</button>
  </form>

  <h3>Total: $<?= number_format($total,2) ?></h3>
  <a class="btn" href="/?p=checkout">Proceed to Checkout</a>
<?php endif; ?>

<?php require "includes/db.php"; ?>
<!DOCTYPE html>
<html>
<head>
    <title>My Cart</title>
</head>
<body>

<h2>My Cart</h2>

<?php
$total = 0;

if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $id) {
        $car = $conn->query("SELECT * FROM cars WHERE id=$id")->fetch_assoc();
        echo "<p>{$car['model']} - $ {$car['price']}</p>";
        $total += $car['price'];
    }
} else {
    echo "Cart is empty";
}
?>

<h3>Total: $<?= number_format($total) ?></h3>

<a href="checkout.php">Place Order</a>

</body>
</html>
<a href="remove_cart.php?id=<?= $id ?>">Remove</a>

<?php
session_start();
require 'db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Initialize cart
$cart = $_SESSION['cart'] ?? [];

$total = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Cart | CarsSaleAftab</title>

<style>
body {
    font-family: Arial, sans-serif;
    background: #f4f6fb;
    padding: 30px;
}
h1 {
    margin-bottom: 20px;
}
.cart-table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
}
.cart-table th, .cart-table td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
    text-align: left;
}
.cart-table th {
    background: #0f172a;
    color: #fff;
}
.total-box {
    margin-top: 20px;
    font-size: 20px;
    font-weight: bold;
}
.actions {
    margin-top: 25px;
    display: flex;
    gap: 12px;
}
.btn {
    padding: 10px 16px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: bold;
}
.btn.checkout {
    background: #22c55e;
    color: #fff;
}
.btn.back {
    background: #2563eb;
    color: #fff;
}
.btn.remove {
    background: #ef4444;
    color: #fff;
    padding: 6px 10px;
}
.empty {
    background: #fff;
    padding: 20px;
}
</style>
</head>

<body>

<h1>🛒 My Cart</h1>

<?php if (empty($cart)): ?>
    <div class="empty">
        <p>Your cart is empty.</p>
        <a class="btn back" href="home.php">Browse Cars</a>
    </div>
<?php else: ?>

<table class="cart-table">
<tr>
    <th>Car</th>
    <th>Year</th>
    <th>Price</th>
    <th>Qty</th>
    <th>Subtotal</th>
    <th>Action</th>
</tr>

<?php
$ids = implode(',', array_keys($cart));
$query = "SELECT car_id, make, model, year, price FROM cars WHERE car_id IN ($ids)";
$result = $conn->query($query);

while ($car = $result->fetch_assoc()):
    $qty = $cart[$car['car_id']];
    $subtotal = $car['price'] * $qty;
    $total += $subtotal;
?>
<tr>
    <td><?= htmlspecialchars($car['make'] . ' ' . $car['model']) ?></td>
    <td><?= $car['year'] ?></td>
    <td>$<?= number_format($car['price'], 2) ?></td>
    <td><?= $qty ?></td>
    <td>$<?= number_format($subtotal, 2) ?></td>
    <td>
        <a class="btn remove" href="remove_from_cart.php?car_id=<?= $car['car_id'] ?>">Remove</a>
    </td>
</tr>
<?php endwhile; ?>

</table>

<div class="total-box">
    Total: $<?= number_format($total, 2) ?>
</div>

<div class="actions">
    <a class="btn back" href="home.php">Continue Shopping</a>
    <a class="btn checkout" href="checkout.php">Proceed to Checkout</a>
</div>

<?php endif; ?>

</body>
</html>
