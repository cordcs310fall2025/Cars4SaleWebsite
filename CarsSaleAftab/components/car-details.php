<?php include "../components/header.php"; ?>
<?php include "../config/db.php"; ?>

<?php
$id = $_GET["id"];
$car = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM cars WHERE id=$id"));
?>

<h2><?= $car['model']; ?> Details</h2>

<img src="/assets/images/<?= $car['image']; ?>" class="detail-img">

<ul>
    <li>Price: $<?= $car['price']; ?></li>
    <li>Build Type: <?= $car['build_type']; ?></li>
    <li>Car Type: <?= $car['car_type']; ?></li>
    <li>Location: <?= $car['location']; ?></li>
</ul>

<a class="btn" href="index.php?page=seller-info&id=<?= $car['id']; ?>">Seller Contact Info</a>
<a class="btn" href="index.php?page=payment&id=<?= $car['id']; ?>">Payment Details</a>

<?php include "../components/footer.php"; ?>
