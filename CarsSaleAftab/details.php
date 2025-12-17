<?php
session_start();
require 'db.php';

$make = $_GET['make'] ?? null;
$model = $_GET['model'] ?? null;
$year = isset($_GET['year']) ? intval($_GET['year']) : null;

if (!$make || !$model || !$year) {
    echo "Missing parameters. Use ?make=...&model=...&year=...";
    exit;
}

// Fetch variants for this model-year
$stmt = $conn->prepare("SELECT * FROM cars WHERE make=? AND model=? AND year=? ORDER BY price ASC");
$stmt->bind_param("ssi", $make, $model, $year);
$stmt->execute();
$res = $stmt->get_result();
$variants = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><title>Details: <?= htmlspecialchars($make . ' ' . $model . ' ' . $year); ?></title>
<style>
body{font-family:Arial,Helvetica,sans-serif;background:#f6f9fc;padding:20px}
.container{max-width:1000px;margin:20px auto;background:#fff;padding:18px;border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,0.06)}
.header{display:flex;justify-content:space-between;align-items:center}
.cards{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px;margin-top:14px}
.card{background:#fff;border-radius:8px;overflow:hidden;border:1px solid #eef3fb}
.card img{width:100%;height:200px;object-fit:cover}
.card-body{padding:12px}
.btn{display:inline-block;padding:8px 12px;border-radius:6px;text-decoration:none;background:#007bff;color:#fff}
</style>
</head>
<body>
<div class="container">
  <div class="header">
    <h2><?= htmlspecialchars($make . ' ' . $model . ' — ' . $year); ?></h2>
    <div><a href="home.php">Back to Home</a></div>
  </div>

  <?php if(empty($variants)): ?>
    <p>No variants found for this model/year.</p>
  <?php else: ?>
    <div class="cards">
      <?php foreach($variants as $v): ?>
        <div class="card">
          <img src="<?= htmlspecialchars($v['image'] ?: 'https://via.placeholder.com/720x480?text=' . urlencode($v['make'] . ' ' . $v['model'])); ?>" alt="<?= htmlspecialchars($v['title']); ?>">
          <div class="card-body">
            <h3><?= htmlspecialchars($v['title'] ?: ($v['make'].' '.$v['model'].' '.$v['trim'])); ?></h3>
            <p><strong>Trim:</strong> <?= htmlspecialchars($v['trim']); ?> | <strong>Year:</strong> <?= intval($v['year']); ?></p>
            <p><strong>Price:</strong> $<?= number_format($v['price'],2); ?></p>
            <p><?= nl2br(htmlspecialchars(substr($v['description'],0,200))); ?><?php if(strlen($v['description'])>200) echo '...'; ?></p>
            <div style="margin-top:8px;">
              <a class="btn" href="create_order.php?user_id=<?= urlencode($_SESSION['user_id'] ?? ''); ?>&car_id=<?= urlencode($v['car_id']); ?>">Buy Now</a>
              <a class="btn" style="background:#ffd24d;color:#071028" href="add_cart.php?user_id=<?= urlencode($_SESSION['user_id'] ?? ''); ?>&car_id=<?= urlencode($v['car_id']); ?>">Add to Cart</a>
              <a class="btn" style="background:#6b7280;margin-left:8px" href="build.php?make=<?= urlencode($make); ?>&model=<?= urlencode($model); ?>&year=<?= urlencode($year); ?>&car_id=<?= urlencode($v['car_id']); ?>">Build</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
</body>
</html>
