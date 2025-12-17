<?php
// pages/shop.php
// $pdo is available from index.php (db.php included earlier)
$stmt = $pdo->query("SELECT * FROM cars ORDER BY created_at DESC");
$cars = $stmt->fetchAll();
?>
<h1>Available Cars</h1>
<div class="car-grid">
<?php foreach ($cars as $car): ?>
  <div class="car-card">
    <img src="<?= htmlspecialchars('assets/images/'.$car['image']) ?>" alt="">
    <h3><?= htmlspecialchars($car['make'].' '.$car['model']) ?></h3>
    <p>Year: <?= htmlspecialchars($car['year']) ?></p>
    <p class="price">$<?= number_format($car['price'],2) ?></p>
    <a href="/?p=car&id=<?= $car['id'] ?>" class="btn">View</a>
    <form method="POST" action="/?p=cart" style="display:inline">
      <input type="hidden" name="action" value="add">
      <input type="hidden" name="car_id" value="<?= $car['id'] ?>">
      <input type="hidden" name="qty" value="1">
      <button class="btn">Add to cart</button>
    </form>
  </div>
<?php endforeach; ?>
</div>
