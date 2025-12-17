<?php
session_start();
include "../db.php";
if (!isset($_SESSION['admin'])) { header("Location: index.php"); exit(); }

$cars = mysqli_query($conn, "SELECT * FROM cars ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cars Management</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>

<?php include "navbar.php"; ?>

<div class="container">
    <h1>All Cars</h1>
    <a href="add_car.php" class="add-btn">+ Add Car</a>

    <table class="table">
        <tr>
            <th>Image</th><th>Model</th><th>Trim</th><th>Price</th><th>Action</th>
        </tr>

        <?php while($c = mysqli_fetch_assoc($cars)): ?>
        <tr>
            <td><img src="uploads/<?php echo $c['image']; ?>" width="100"></td>
            <td><?php echo $c['model']; ?></td>
            <td><?php echo $c['trim']; ?></td>
            <td>$<?php echo number_format($c['price']); ?></td>
            <td>
                <a href="edit_car.php?id=<?php echo $c['id']; ?>">Edit</a> |
                <a href="delete_car.php?id=<?php echo $c['id']; ?>" onclick="return confirm('Delete?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>
