<?php
session_start();
require_once __DIR__ . '/../db.php';
include "includes/nav.php";

// Prepare data for charts (last 30 days)
$labels = [];
$views_data = [];
$sales_data = [];
for ($i=29;$i>=0;$i--){
    $d = date('Y-m-d', strtotime("-{$i} days"));
    $labels[] = $d;
    $r = $conn->prepare("SELECT COUNT(*) as cnt FROM car_views WHERE DATE(viewed_at)=?");
    $r->bind_param("s",$d); $r->execute(); $v = $r->get_result()->fetch_assoc()['cnt']; $views_data[] = intval($v); $r->close();
    $s = $conn->prepare("SELECT COUNT(*) as cnt, COALESCE(SUM(total_price),0) as rev FROM sales WHERE DATE(sale_date)=?");
    $s->bind_param("s",$d); $s->execute(); $sr = $s->get_result()->fetch_assoc(); $sales_data[] = intval($sr['cnt']); $s->close();
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Admin Dashboard</title>
<link rel="stylesheet" href="css/admin.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head><body>
<div class="container">
  <h1>Dashboard</h1>
  <div style="display:flex;gap:20px;flex-wrap:wrap">
    <div style="flex:1;min-width:320px;background:#0f1724;padding:16px;border-radius:12px">
      <canvas id="viewsChart"></canvas>
    </div>
    <div style="flex:1;min-width:320px;background:#0f1724;padding:16px;border-radius:12px">
      <canvas id="salesChart"></canvas>
    </div>
  </div>
</div>

<script>
const labels = <?= json_encode($labels); ?>;
const viewsData = <?= json_encode($views_data); ?>;
const salesData = <?= json_encode($sales_data); ?>;

new Chart(document.getElementById('viewsChart'), {
  type: 'line', data: { labels: labels, datasets: [{ label: 'Page Views', data: viewsData, borderColor: '#00c2ff', backgroundColor: 'rgba(0,194,255,0.12)', fill:true }]},
  options: { responsive:true, maintainAspectRatio:false }
});

new Chart(document.getElementById('salesChart'), {
  type: 'bar', data: { labels: labels, datasets: [{ label: 'Sales (count)', data: salesData, backgroundColor: 'rgba(255,160,64,0.9)' }]},
  options: { responsive:true, maintainAspectRatio:false }
});
</script>

</body></html>

<?php
require '../db.php';
session_start();
if ($_SESSION['role'] !== 'admin') exit;

// KPIs
$totalOrders = $conn->query("SELECT COUNT(*) AS total FROM orders")->fetch_assoc()['total'];
$totalRevenue = $conn->query("SELECT SUM(total_amount) AS revenue FROM orders")->fetch_assoc()['revenue'];
$totalCars = $conn->query("SELECT COUNT(*) AS total FROM cars")->fetch_assoc()['total'];
$totalUsers = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
?>

<h2>Admin Dashboard</h2>

<div class="kpi-grid">
  <div class="kpi">Orders<br><strong><?= $totalOrders ?></strong></div>
  <div class="kpi">Revenue<br><strong>$<?= number_format($totalRevenue,2) ?></strong></div>
  <div class="kpi">Cars<br><strong><?= $totalCars ?></strong></div>
  <div class="kpi">Users<br><strong><?= $totalUsers ?></strong></div>
</div>

<style>
.kpi-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:20px}
.kpi{background:#111;color:#fff;padding:20px;border-radius:12px;text-align:center}
</style>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<canvas id="revenueChart"></canvas>

<script>
new Chart(document.getElementById('revenueChart'), {
  type: 'line',
  data: {
    labels: <?= json_encode($dates) ?>,
    datasets: [{
      label: 'Revenue',
      data: <?= json_encode($totals) ?>,
      borderWidth: 3,
      fill: true
    }]
  }
});
</script>

<?php
session_start();
require '../db.php';

if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'super_admin') {
    header("Location: ../index.php");
    exit;
}

// KPIs
$totalOrders = $conn->query("SELECT COUNT(*) FROM orders")->fetch_row()[0];
$totalRevenue = $conn->query("SELECT SUM(total_amount) FROM orders")->fetch_row()[0];
$totalUsers = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
$totalCars = $conn->query("SELECT COUNT(*) FROM cars")->fetch_row()[0];
?>

<h1>📊 Admin Dashboard</h1>

<div class="stats-grid">
    <div class="stat-box">Total Orders<br><strong><?= $totalOrders ?></strong></div>
    <div class="stat-box">Total Revenue<br><strong>$<?= number_format($totalRevenue,2) ?></strong></div>
    <div class="stat-box">Users<br><strong><?= $totalUsers ?></strong></div>
    <div class="stat-box">Cars Listed<br><strong><?= $totalCars ?></strong></div>
</div>

<a href="manage_users.php">Manage Users</a> |
<a href="manage_cars.php">Manage Cars</a> |
<a href="manage_orders.php">Manage Orders</a>

<style>
.stats-grid {
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
    gap:20px;
}
.stat-box {
    padding:20px;
    background:#111;
    color:#fff;
    border-radius:10px;
    text-align:center;
    font-size:18px;
}
</style>

<?php
require "../includes/db.php";
require "../includes/auth.php";
?>

<h2>Admin Dashboard</h2>
<a href="add_car.php">Add New Car</a>
<a href="edit_car.php">Manage Cars</a>
