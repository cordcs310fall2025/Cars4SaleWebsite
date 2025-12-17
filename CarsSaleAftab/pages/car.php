<?php
// pages/car.php
$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
$stmt->execute([$id]);
$car = $stmt->fetch();
if (!$car) { echo "<p>Car not found.</p>"; return; }
?>
<div class="car-details">
  <img src="<?= 'assets/images/'.$car['image'] ?>" alt="">
  <h2><?= htmlspecialchars($car['make'].' '.$car['model']) ?></h2>
  <p><?= nl2br(htmlspecialchars($car['description'])) ?></p>
  <p class="price">$<?= number_format($car['price'],2) ?></p>

  <form method="POST" action="/?p=cart">
    <input type="hidden" name="action" value="add">
    <input type="hidden" name="car_id" value="<?= $car['id'] ?>">
    Quantity: <input type="number" name="qty" value="1" min="1">
    <button class="btn">Add to cart</button>
  </form>
</div>
