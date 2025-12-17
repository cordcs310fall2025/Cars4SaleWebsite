<?php
// pages/checkout.php
if (empty($_SESSION['user'])) {
    $_SESSION['flash'] = "Please login to checkout.";
    header("Location: /?p=login");
    exit;
}

$cart = $_SESSION['cart'] ?? [];
if (!$cart) {
    echo "<p>Your cart is empty. <a href='/?p=shop'>Shop now</a></p>";
    return;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // For demo: we do a simple checkout (no real payment).
    $pdo->beginTransaction();
    try {
        $total = array_reduce($cart, function($sum,$it){ return $sum + $it['price']*$it['qty']; }, 0);
        $stmt = $pdo->prepare("INSERT INTO orders (user_id,total,status) VALUES (?,?,?)");
        $stmt->execute([$_SESSION['user']['id'],$total,'processing']);
        $order_id = $pdo->lastInsertId();

        $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id,car_id,qty,price) VALUES (?,?,?,?)");
        foreach ($cart as $it) {
            $stmtItem->execute([$order_id, $it['id'], $it['qty'], $it['price']]);
        }
        $pdo->commit();
        // clear cart
        unset($_SESSION['cart']);
        $_SESSION['flash'] = "Order placed successfully. Order ID: $order_id";
        header("Location: /?p=shop");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Checkout failed. Please try again.";
    }
}

// show confirmation form
$total = array_reduce($cart, function($s,$it){ return $s + $it['price']*$it['qty']; }, 0);
?>
<h1>Checkout</h1>
<?php if (!empty($error)) echo "<p class='error'>".htmlspecialchars($error)."</p>"; ?>
<h3>Order total: $<?= number_format($total,2) ?></h3>
<form method="POST" action="?p=checkout">
  <p>Billing name: <input name="billing_name" value="<?= htmlspecialchars($_SESSION['user']['name']) ?>" required></p>
  <p>Email: <input name="email" type="email" value="<?= htmlspecialchars($_SESSION['user']['email']) ?>" required></p>
  <!-- For real site: integrate with Stripe/PayPal here -->
  <button>Place Order (Demo)</button>
</form>

<?php
require "includes/db.php";

$total = 0;
foreach ($_SESSION['cart'] as $id) {
    $car = $conn->query("SELECT price FROM cars WHERE id=$id")->fetch_assoc();
    $total += $car['price'];
}

$conn->query("INSERT INTO orders (total_amount) VALUES ($total)");
$order_id = $conn->insert_id;

$_SESSION['last_order'] = $order_id;
unset($_SESSION['cart']);

header("Location: receipt.php");
exit;
