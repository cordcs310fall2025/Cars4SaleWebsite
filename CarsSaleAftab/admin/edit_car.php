<?php
session_start();
include "../db.php";

if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];
$car = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM cars WHERE id=$id"));

if (isset($_POST['update'])) {
    $model = $_POST['model'];
    $trim = $_POST['trim'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    // If new image uploaded
    if ($_FILES['image']['name'] != "") {
        $image = $_FILES['image']['name'];
        $tmp = $_FILES['image']['tmp_name'];
        move_uploaded_file($tmp, "uploads/$image");

        mysqli_query($conn, "UPDATE cars SET 
            model='$model',
            trim='$trim',
            price='$price',
            description='$description',
            image='$image'
        WHERE id=$id");

    } else {
        mysqli_query($conn, "UPDATE cars SET 
            model='$model',
            trim='$trim',
            price='$price',
            description='$description'
        WHERE id=$id");
    }

    header("Location: cars.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Car</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>

<?php include "navbar.php"; ?>

<div class="container">
    <h1>Edit Car</h1>

    <form method="post" enctype="multipart/form-data">

        <label>Model</label>
        <input type="text" name="model" value="<?php echo $car['model']; ?>" required>

        <label>Trim</label>
        <input type="text" name="trim" value="<?php echo $car['trim']; ?>" required>

        <label>Price</label>
        <input type="number" name="price" value="<?php echo $car['price']; ?>" required>

        <label>Description</label>
        <textarea name="description"><?php echo $car['description']; ?></textarea>

        <label>Current Image</label><br>
        <img src="uploads/<?php echo $car['image']; ?>" width="200" style="border-radius:12px;"><br><br>

        <label>Upload New Image (optional)</label>
        <input type="file" name="image">

        <button type="submit" name="update">Save Changes</button>

    </form>

</div>
</body>
</html>
