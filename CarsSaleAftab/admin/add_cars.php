<?php
session_start();
include "../db.php";
if (!isset($_SESSION['admin'])) { header("Location: index.php"); exit(); }

if (isset($_POST['save'])) {
    $model = $_POST['model'];
    $trim = $_POST['trim'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    $image = $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];
    move_uploaded_file($tmp, "uploads/$image");

    mysqli_query($conn, "INSERT INTO cars(model, trim, price, image, description)
                         VALUES('$model', '$trim', '$price', '$image', '$description')");

    header("Location: cars.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Car</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>

<?php include "navbar.php"; ?>

<div class="container">
    <h1>Add New Car</h1>

    <form method="post" enctype="multipart/form-data">
        <input type="text" name="model" placeholder="Model" required>
        <input type="text" name="trim" placeholder="Trim" required>
        <input type="number" name="price" placeholder="Price" required>
        <textarea name="description" placeholder="Description"></textarea>
        <input type="file" name="image" required>
        <button type="submit" name="save">Save</button>
    </form>

</div>
</body>
</html>
