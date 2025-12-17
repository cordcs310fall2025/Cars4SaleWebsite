<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Purchase Complete 🎉</title>

<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">

<style>
body {
  margin:0;
  font-family:'Inter',sans-serif;
  background: radial-gradient(circle at top,#0f172a,#020617);
  color:#e5e7eb;
}

/* Celebration Header */
.hero {
  padding:80px 20px;
  text-align:center;
  background:
    linear-gradient(180deg,rgba(15,23,42,.95),rgba(2,6,23,.98)),
    url("<?= htmlspecialchars($car['image']) ?>") center/cover no-repeat;
}

.hero h1 {
  font-family:'Montserrat',sans-serif;
  font-size:40px;
  margin-bottom:12px;
}

.hero p {
  font-size:18px;
  color:#cbd5f5;
}

/* Card */
.card {
  max-width:900px;
  margin:-60px auto 40px;
  background:linear-gradient(180deg,#ffffff,#f8fafc);
  color:#020617;
  border-radius:20px;
  box-shadow:0 40px 80px rgba(0,0,0,.4);
  overflow:hidden;
}

.card-header {
  padding:30px;
  border-bottom:1px solid #e5e7eb;
}

.card-header h2 {
  margin:0;
  font-family:'Montserrat',sans-serif;
}

.card-body {
  display:grid;
  grid-template-columns:1.1fr 1fr;
  gap:30px;
  padding:30px;
}

.card-body img {
  width:100%;
  border-radius:16px;
  object-fit:cover;
}

/* Details */
.details h3 {
  margin-top:0;
  font-family:'Montserrat',sans-serif;
}

.details ul {
  list-style:none;
  padding:0;
  margin:20px 0;
}

.details li {
  padding:10px 0;
  border-bottom:1px solid #e5e7eb;
  display:flex;
  justify-content:space-between;
}

/* Total */
.total {
  margin-top:20px;
  padding:20px;
  background:#020617;
  color:#f8fafc;
  border-radius:14px;
  text-align:center;
}

.total span {
  font-size:26px;
  font-weight:700;
}

/* Actions */
.actions {
  padding:25px;
  display:flex;
  justify-content:space-between;
  gap:15px;
  flex-wrap:wrap;
}

.actions a {
  text-decoration:none;
  padding:14px 22px;
  border-radius:999px;
  font-weight:700;
}

.btn-primary {
  background:linear-gradient(180deg,#2563eb,#1e40af);
  color:#fff;
}

.btn-outline {
  border:2px solid #020617;
  color:#020617;
}

/* Footer Message */
.footer {
  text-align:center;
  padding:30px;
  color:#94a3b8;
  font-size:14px;
}

@media(max-width:800px){
  .card-body { grid-template-columns:1fr; }
}
</style>
</head>

<body>

<!-- HERO -->
<section class="hero">
  <h1>Congratulations, <?= htmlspecialchars($_SESSION['username']) ?> 🎉</h1>
  <p>Your new <?= htmlspecialchars($car['make'].' '.$car['model']) ?> is officially yours</p>
</section>

<!-- PURCHASE CARD -->
<div class="card">

  <div class="card-header">
    <h2>Purchase Summary</h2>
    <p>Order #<?= $order['order_id'] ?> • <?= date("F j, Y") ?></p>
  </div>

  <div class="card-body">

    <img src="<?= htmlspecialchars($car['image']) ?>" alt="Car Image">

    <div class="details">
      <h3><?= htmlspecialchars($car['make'].' '.$car['model']) ?> (<?= $car['year'] ?>)</h3>

      <ul>
        <li><span>Trim</span><strong><?= htmlspecialchars($car['title']) ?></strong></li>
        <li><span>Price</span><strong>$<?= number_format($car['price'],2) ?></strong></li>
        <li><span>Quantity</span><strong><?= $order['quantity'] ?></strong></li>
        <li><span>Status</span><strong>Completed</strong></li>
      </ul>

      <div class="total">
        Total Paid<br>
        <span>$<?= number_format($order['total_amount'],2) ?></span>
      </div>
    </div>

  </div>

  <!-- ACTIONS -->
  <div class="actions">
    <a href="home.php" class="btn-outline">← Continue Browsing</a>
    <a href="javascript:window.print()" class="btn-primary">Print Receipt</a>
  </div>

</div>

<div class="footer">
  Thank you for choosing <strong>CarsSaleAftab</strong> — Drive safe 🚗
</div>

</body>
</html>
