<?php
session_start();
require 'db.php';

// Redirect to login if user      
// not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Handle logout (simple)
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

// Price filter (min/max from GET)
$min_price = isset($_GET['min_price']) && is_numeric($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) && is_numeric($_GET['max_price']) ? (float)$_GET['max_price'] : 10000000;

// Helper: fetch distinct model groups (make+model+year) within price range
$sql_groups = "SELECT make, model, year, MIN(price) AS min_price, MAX(price) AS max_price
               FROM cars
               WHERE price BETWEEN ? AND ?
               GROUP BY make, model, year
               ORDER BY year DESC, make ASC, model ASC";
$stmt = $conn->prepare($sql_groups);
$stmt->bind_param("dd", $min_price, $max_price);
$stmt->execute();
$groups = $stmt->get_result();
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>CarsSaleAftab — Build & Price</title>

<!-- Google fonts -->
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">

<style>
/* Basic reset */
* { box-sizing: border-box; margin: 0; padding: 0; }

/* Page background & layout */
body { font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; background: linear-gradient(180deg,#f3f6fb,#ecf1f8); color: #0b1220; line-height:1.4; }
.header {
  background: linear-gradient(90deg,#0f1724,#071027);
  color: #fff;
  padding: 28px 40px;
  display:flex;
  justify-content:space-between;
  align-items:center;
}
.brand { font-family: 'Montserrat', sans-serif; font-size:22px; font-weight:700; letter-spacing:0.6px; }
.header .nav { display:flex; gap:14px; align-items:center; }
.header .nav a { color: #e6eefc; text-decoration:none; font-weight:600; padding:6px 10px; border-radius:6px; }
.header .nav a:hover { color:#fff; background: rgba(255,255,255,0.03); }

.container { width: 94%; max-width:1200px; margin: 28px auto; }

/* Top hero / controls */
.hero {
  display:flex; justify-content:space-between; gap:20px; align-items:center; margin-bottom:20px;
}
.hero .left {
  background: rgba(255,255,255,0.7); backdrop-filter: blur(6px);
  padding:18px 20px; border-radius:12px; box-shadow: 0 6px 20px rgba(12,22,40,0.08);
  flex:1;
}
.hero h1 { font-family:'Montserrat',sans-serif; font-size:20px; color:#071028; margin-bottom:6px; }
.hero p { color:#334155; font-size:14px; margin-bottom:8px; }

/* Filter controls */
.controls {
  display:flex; gap:10px; align-items:center; flex-wrap:wrap;
}
.controls input[type=number] { width:120px; padding:8px 10px; border-radius:8px; border:1px solid #d1dbe8; background:#fff; }
.controls button { padding:9px 14px; border-radius:8px; background: linear-gradient(180deg,#ffbf00,#e0a300); border:none; color:#071027; font-weight:700; cursor:pointer; }
.controls button:hover { filter:brightness(.98); }

/* Grid of models */
.grid {
  display:grid;
  grid-template-columns: repeat(auto-fill,minmax(300px,1fr));
  gap:22px;
  margin-top:18px;
}

/* Model card */
.card {
  background: linear-gradient(180deg, rgba(255,255,255,0.85), rgba(245,249,255,0.85));
  border-radius:14px;
  overflow:hidden;
  box-shadow: 0 10px 30px rgba(12,22,40,0.08);
  display:flex;
  flex-direction:column;
  transition: transform .18s ease, box-shadow .18s ease;
}
.card:hover { transform: translateY(-8px); box-shadow:0 18px 40px rgba(12,22,40,0.12); }

/* Image area */
.card .media { height:190px; width:100%; position:relative; background: #e9eef8; display:flex; align-items:center; justify-content:center; }
.card .media img { width:100%; height:100%; object-fit:cover; display:block; }

/* Info area */
.card .info { padding:14px 16px; display:flex; flex-direction:column; gap:10px; }
.card .info .title { font-family:'Montserrat',sans-serif; font-weight:700; font-size:16px; color:#071028; }
.card .specs { color:#334155; font-size:13px; display:flex; gap:12px; flex-wrap:wrap; }
.card .price-row { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-top:6px; }
.card .price { font-weight:700; color:#071028; font-size:18px; }
.card .actions { display:flex; gap:8px; margin-top:12px; }
.btn {
  padding:9px 12px; border-radius:10px; text-decoration:none; font-weight:700; font-size:13px; display:inline-flex; align-items:center; gap:8px;
}
.btn.details { background:transparent; color:#0b1220; border:1px solid #dbe7fb; }
.btn.build { background: linear-gradient(180deg,#0ea5a4,#0b9390); color:#fff; }
.btn.cart { background: linear-gradient(180deg,#ffb84d,#ff9f00); color:#071028; }

/* trims list */
.trims { display:flex; gap:8px; flex-wrap:wrap; margin-top:8px; }
.trim-pill { padding:6px 10px; background: rgba(7,16,40,0.06); border-radius:999px; font-weight:600; font-size:13px; color:#071028; }

/* small responsive tweaks */
@media (max-width:900px){
  .hero { flex-direction:column; align-items:stretch; }
  .controls input[type=number] { width:100%; }
}
</style>
</head>
<body>

<header class="header">
  <div class="brand">CarsSaleAftab</div>
  <div class="nav">
    <span style="color:#cfe9ff; margin-right:12px;">Welcome, <?= htmlspecialchars($_SESSION['username']); ?></span>
    <a href="cart.php">My Cart</a>
    <a href="add_car.php">Add Car</a>
    <a href="home.php?logout=1" style="background:rgba(255,255,255,0.06); padding:6px 10px; border-radius:8px;">Logout</a>
  </div>
</header>

<div class="container">

  <!-- Hero + Filters -->
  <div class="hero">
    <div class="left">
      <h1>BUILD &amp; PRICE — Create Your Perfect Vehicle</h1>
      <p>Select a model below to view trims, pricing and configuration options. Use the price filter to narrow results.</p>

      <div style="margin-top:10px;" class="controls">
        <form method="GET" action="home.php" style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
          <label>Min $ <input type="number" name="min_price" value="<?= htmlspecialchars($min_price); ?>" min="0"></label>
          <label>Max $ <input type="number" name="max_price" value="<?= htmlspecialchars($max_price); ?>" min="0"></label>
          <button type="submit">Filter</button>
          <a href="home.php" style="padding:9px 12px; border-radius:8px; background:#fff; color:#071028; font-weight:700; text-decoration:none;">Reset</a>
        </form>
      </div>
    </div>

    <!-- quick stats (counts) -->
    <div style="min-width:220px; display:flex; flex-direction:column; gap:10px; align-items:flex-end;">
      <div style="background:#fff; padding:12px 16px; border-radius:12px; box-shadow:0 6px 18px rgba(12,22,40,0.06);">
        <div style="font-size:12px;color:#667085;">Results</div>
        <div style="font-weight:700; font-size:18px; margin-top:4px;">
          <?php
          // compute total number of model groups found
          $groups->data_seek(0);
          $total_groups = $groups->num_rows;
          echo $total_groups;
          ?>
        </div>
      </div>
      <div style="background:linear-gradient(180deg,#fff,#f8fbff); padding:12px 16px; border-radius:12px; box-shadow:0 6px 18px rgba(12,22,40,0.04);">
        <div style="font-size:12px;color:#667085;">User</div>
        <div style="font-weight:700; font-size:16px; margin-top:4px;"><?= htmlspecialchars($_SESSION['username']); ?></div>
      </div>
    </div>
  </div>

  <!-- Grid of models -->
  <div class="grid">
    <?php
    // iterate over groups: each group = a make+model+year
    $groups->data_seek(0);
    while ($g = $groups->fetch_assoc()):
      $g_make = $g['make'];
      $g_model = $g['model'];
      $g_year = $g['year'];

      // get variants (rows) that match this make+model+year, ordered by price (smallest first)
      $sql_variants = "SELECT * FROM cars WHERE make = ? AND model = ? AND year = ? ORDER BY price ASC";
      $s2 = $conn->prepare($sql_variants);
      $s2->bind_param("ssi", $g_make, $g_model, $g_year);
      $s2->execute();
      $variants = $s2->get_result();

      // pick a representative image (first non-empty) or placeholder
      $rep_img = '';
      $variants->data_seek(0);
      while ($v = $variants->fetch_assoc()) {
        if (!empty($v['image'])) { $rep_img = $v['image']; break; }
      }
      if (empty($rep_img)) {
        $rep_img = 'https://via.placeholder.com/720x480?text=' . urlencode($g_make . ' ' . $g_model);
      }

      // reset pointer to iterate trims again below
      $variants->data_seek(0);

      // form model title
      $title = htmlspecialchars($g_make . ' ' . $g_model);
    ?>
      <article class="card" aria-labelledby="title-<?= md5($title.$g_year); ?>">
        <div class="media">
          <img src="<?= htmlspecialchars($rep_img); ?>" alt="<?= htmlspecialchars($title . ' ' . $g_year); ?>">
        </div>

        <div class="info">
          <div>
            <div class="title" id="title-<?= md5($title.$g_year); ?>"><?= $title; ?> <span style="font-weight:600;color:#475569; font-size:13px;">&nbsp;<?= $g_year; ?></span></div>
            <div class="specs">
              <div>Seats: Up to 5</div>
              <div>Drivetrain: FWD / AWD</div>
              <div>Availability: In stock</div>
            </div>
          </div>

          <!-- trims -->
          <div class="trims">
            <?php
            $trim_count = 0;
            $min_variant_price = null;
            while ($variant = $variants->fetch_assoc()) {
              $trim_count++;
              $trim_name = isset($variant['title']) && !empty($variant['title']) ? $variant['title'] : ('Trim ' . $trim_count);
              $trim_price = number_format($variant['price'], 2);
              if ($min_variant_price === null) $min_variant_price = $variant['price'];
              echo '<div class="trim-pill">' . htmlspecialchars($trim_name) . ' — $' . $trim_price . '</div>';
            }
            // if no explicit variants (trim_count == 0), show placeholder trims
            if ($trim_count === 0) {
              // simulate 3 trims using the group's min and increments
              $base = (float)$g['min_price'];
              if ($base <= 0) $base = 29990;
              $names = ['S','SV','Platinum'];
              for ($i=0;$i<3;$i++){
                $p = number_format($base + ($i*2000),2);
                echo '<div class="trim-pill">'.htmlspecialchars($names[$i]).' — $'.$p.'</div>';
                if ($i===0) $min_variant_price = $base;
              }
            }
            ?>
          </div>

          <div class="price-row">
            <div class="price">Starting at $<?= number_format($min_variant_price ?? $g['min_price'] ?? 0, 2); ?></div>
            <div style="font-size:13px;color:#475569;">From <?= htmlspecialchars($g_make); ?></div>
          </div>

          <div class="actions">
            <!-- More details: link to details page (pass make/model/year) -->
            <a class="btn details" href="details.php?make=<?= urlencode($g_make); ?>&model=<?= urlencode($g_model); ?>&year=<?= urlencode($g_year); ?>">More Details</a>

            <!-- Build: takes to a build page for the model -->
            <a class="btn build" href="build.php?make=<?= urlencode($g_make); ?>&model=<?= urlencode($g_model); ?>&year=<?= urlencode($g_year); ?>">Build</a>

            <!-- For Add to Cart / Place Order we pick the cheapest variant's car_id if exists -->
            <?php
            // fetch cheapest variant id quickly
            $s3 = $conn->prepare("SELECT car_id, price FROM cars WHERE make = ? AND model = ? AND year = ? ORDER BY price ASC LIMIT 1");
            $s3->bind_param("ssi", $g_make, $g_model, $g_year);
            $s3->execute();
            $r3 = $s3->get_result();
            $cheapest = $r3->fetch_assoc();
            if ($cheapest) {
              $cheapest_id = $cheapest['car_id'];
              $cheapest_price = $cheapest['price'];
            } else {
              $cheapest_id = null;
              $cheapest_price = 0;
            }
            ?>
            <?php if ($cheapest_id): ?>
              <a class="btn cart" href="add_cart.php?user_id=<?= urlencode($_SESSION['user_id']); ?>&car_id=<?= urlencode($cheapest_id); ?>">Add to Cart</a>
              <a class="btn build" style="background:linear-gradient(180deg,#2563eb,#1e40af);" href="create_order.php?user_id=<?= urlencode($_SESSION['user_id']); ?>&car_id=<?= urlencode($cheapest_id); ?>">Place Order</a>
            <?php else: ?>
              <span style="color:#94a3b8;font-weight:600;padding:8px 12px;border-radius:8px;">No variants</span>
            <?php endif; ?>

          </div>
        </div>
      </article>
    <?php
      $s2->close();
      $s3->close();
    endwhile;
    $stmt->close();
    ?>
  </div>

</div>

<select name="category">
  <option value="">All Categories</option>
  <option value="SUV">SUV</option>
  <option value="Sedan">Sedan</option>
  <option value="Truck">Truck</option>
  <option value="Coupe">Coupe</option>
  <option value="Electric">Electric</option>
</select>

<?php
$imagePath = (!empty($car['image']) && file_exists($car['image']))
    ? $car['image']
    : 'assets/no-car.png';
?>

$targetDir = "uploads/cars/";
$filename = time() . "_" . basename($_FILES["image"]["name"]);
$targetFile = $targetDir . $filename;

move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile);

// Save ONLY this to DB:
$image = $targetFile;


<img src="<?= htmlspecialchars($imagePath) ?>"
     alt="<?= htmlspecialchars($car['title']) ?>"
     loading="lazy">

</body>
</html>

<?php require "includes/db.php"; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
</head>
<body>

<h2>Available Cars</h2>

<?php
$result = $conn->query("SELECT * FROM cars");
while ($car = $result->fetch_assoc()):
?>
<div style="border:1px solid #ccc; padding:10px; margin:10px;">
    <h3><?= $car['model'] ?></h3>
    <p>Price: $<?= number_format($car['price']) ?></p>

    <form method="POST" action="add_cart.php">
        <input type="hidden" name="car_id" value="<?= $car['id'] ?>">
        <button type="submit">Add to Cart</button>
    </form>
</div>
<?php endwhile; ?>

<a href="cart.php">View Cart</a>

</body>
</html>

<a class="btn cart"
   href="add_cart.php?car_id=<?= urlencode($cheapest_id); ?>">
   Add to Cart
</a>
