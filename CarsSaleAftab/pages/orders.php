<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$orders = $conn->query("SELECT * FROM orders WHERE user_id=$user_id ORDER BY created_at DESC");
?>

<h2>My Orders</h2>

<?php while ($order = $orders->fetch_assoc()): ?>
    <div style="border:1px solid #ccc; padding:15px; margin-bottom:15px;">
        <p><strong>Order #<?= $order['order_id'] ?></strong></p>
        <p>Status: <?= $order['status'] ?></p>
        <p>Total: $<?= number_format($order['total_amount'],2) ?></p>

        <ul>
        <?php
        $items = $conn->query("
            SELECT c.model, oi.quantity, oi.price
            FROM order_items oi
            JOIN cars c ON oi.car_id = c.car_id
            WHERE oi.order_id = {$order['order_id']}
        ");
        while ($item = $items->fetch_assoc()):
        ?>
            <li><?= $item['model'] ?> × <?= $item['quantity'] ?> ($<?= $item['price'] ?>)</li>
        <?php endwhile; ?>
        </ul>
    </div>
<?php endwhile; ?>
