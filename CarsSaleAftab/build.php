<?php
session_start();
require 'db.php';

$make = $_GET['make'] ?? null;
$model = $_GET['model'] ?? null;
$year = isset($_GET['year']) ? intval($_GET['year']) : null;
$car_id = isset($_GET['car_id']) ? intval($_GET['car_id']) : null;

// If car_id provided, fetch base data
$base = null;
if ($car_id) {
    $s = $conn->prepare("SELECT * FROM cars WHERE car_id=? LIMIT 1");
    $s->bind_param("i", $car_id);
    $s->execute();
    $base = $s->get_result()->fetch_assoc();
    $s->close();
}

if (!$make || !$model || !$year) {
    echo "Missing parameters.";
    exit;
}

// Simple options (demo) — you can expand these with real DB options
$options = [
    'color' => ['Champagne Silver', 'Pearl White', 'Midnight Black', 'Blue Metallic'],
    'wheels' => ['17" Alloy', '18" Sport Alloy', '19" Premium Alloy'],
    'package' => ['Standard', 'Tech Package (+$1200)', 'Premium Package (+$3000)']
];

$selected = $_POST ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['build'])) {
    // Compose a "build summary" and optionally store to session or DB
    $_SESSION['last_build'] = [
        'make'=>$make, 'model'=>$model, 'year'=>$year, 'car_id'=>$car_id ?? null,
        'options'=>$selected
    ];
    $built = true;
} else {
    $built = false;
}
?>
<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><title>Build: <?= htmlspecialchars("$make $model $year"); ?></title>
<style>
body{font-family:Arial,Helvetica,sans-serif;background:#f6f9fc;padding:20px}
.container{max-width:760px;margin:20px auto;background:#fff;padding:18px;border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,0.06)}
label{display:block;margin-top:10px;font-weight:600}
select{width:100%;padding:10px;border-radius:6px;border:1px solid #dfe7f3}
button{margin-top:14px;padding:10px 14px;background:#0ea5a4;border:none;color:#fff;border-radius:6px}
.summary{background:#f8fafc;padding:12px;border-radius:6px;margin-top:12px}
</style>
</head>
<body>
<div class="container">
  <h2>Build &amp; Price: <?= htmlspecialchars("$make $model $year"); ?></h2>
  <?php if($base): ?>
    <p><strong>Base price:</strong> $<?= number_format($base['price'],2); ?></p>
  <?php endif; ?>

  <?php if($built && isset($_SESSION['last_build'])): ?>
    <div class="summary">
      <h3>Build saved to session</h3>
      <pre><?= htmlspecialchars(print_r($_SESSION['last_build'], true)); ?></pre>
    </div>
    <p><a href="home.php">Back to Home</a></p>
  <?php else: ?>
    <form method="POST">
      <label>Color</label>
      <select name="color">
        <?php foreach($options['color'] as $opt): ?>
          <option value="<?= htmlspecialchars($opt); ?>"><?= htmlspecialchars($opt); ?></option>
        <?php endforeach; ?>
      </select>

      <label>Wheels</label>
      <select name="wheels">
        <?php foreach($options['wheels'] as $opt): ?>
          <option value="<?= htmlspecialchars($opt); ?>"><?= htmlspecialchars($opt); ?></option>
        <?php endforeach; ?>
      </select>

      <label>Package</label>
      <select name="package">
        <?php foreach($options['package'] as $opt): ?>
          <option value="<?= htmlspecialchars($opt); ?>"><?= htmlspecialchars($opt); ?></option>
        <?php endforeach; ?>
      </select>

      <button type="submit" name="build">Build & Save</button>
    </form>
  <?php endif; ?>
</div>
</body>
</html>
