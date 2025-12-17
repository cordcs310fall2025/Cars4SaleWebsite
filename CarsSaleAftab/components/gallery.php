<?php include "../components/header.php"; ?>
<?php include "../config/db.php"; ?>

<h2>Car Gallery</h2>

<div class="gallery-grid">
<?php
$result = mysqli_query($conn, "SELECT * FROM cars");
while ($car = mysqli_fetch_assoc($result)) {
    echo "
        <a href='index.php?page=details&id={$car['id']}'>
            <img src='/assets/images/{$car['image']}' class='gallery-img'>
        </a>
    ";
}
?>
</div>

<?php include "../components/footer.php"; ?>
