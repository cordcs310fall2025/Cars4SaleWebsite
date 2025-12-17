<?php
require 'db.php'; // Connect to database

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $car_id = $_POST['car_id'];
    $quantity = $_POST['quantity'];

    // Get car price
    $result = $conn->query("SELECT price FROM cars WHERE car_id = $car_id");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $total_price = $row['price'] * $quantity;

        $stmt = $conn->prepare("INSERT INTO orders (user_id, car_id, quantity, total_price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiid", $user_id, $car_id, $quantity, $total_price);

        if ($stmt->execute()) {
            echo "Order created successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Car not found.";
    }
}
?>

<!-- Simple HTML form -->
<form method="POST">
    User ID: <input type="number" name="user_id" required><br>
    Car ID: <input type="number" name="car_id" required><br>
    Quantity: <input type="number" name="quantity" value="1"><br>
    <button type="submit">Place Order</button>
</form>

<?php
session_start();
require 'db.php'; // Database connection

// Redirect to login if user is not logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Get car_id from query string or form submission
$car_id = $_GET['car_id'] ?? $_POST['car_id'] ?? null;
$success_message = '';
$error_message = '';

if($car_id) {
    // Fetch car info
    $stmt = $conn->prepare("SELECT * FROM cars WHERE car_id=?");
    $stmt->bind_param("i", $car_id);
    $stmt->execute();
    $car_result = $stmt->get_result();

    if($car_result->num_rows === 0){
        $error_message = "Car not found.";
        $car = null;
    } else {
        $car = $car_result->fetch_assoc();
    }
    $stmt->close();
} else {
    $error_message = "No car selected.";
}

// Handle form submission
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order']) && $car) {
    $quantity = intval($_POST['quantity']);
    if($quantity < 1) $quantity = 1;

    $total_price = $car['price'] * $quantity;

    $stmt = $conn->prepare("INSERT INTO orders (user_id, car_id, quantity, total_price) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiid", $user_id, $car_id, $quantity, $total_price);

    if($stmt->execute()) {
        $success_message = "Order placed successfully! Total: $" . number_format($total_price, 2);
    } else {
        $error_message = "Error placing order: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Place Order - CarsSaleAftab</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
        .container { max-width: 600px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 5px rgba(0,0,0,0.1); }
        h2 { margin-top: 0; }
        input[type=number] { padding: 5px; width: 60px; }
        button { padding: 8px 12px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #218838; }
        .message { margin: 10px 0; padding: 10px; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        a { text-decoration: none; color: #007BFF; }
    </style>
</head>
<body>

<div class="container">
    <a href="index.php">&larr; Back to Home</a>

    <?php if($error_message): ?>
        <div class="message error"><?= htmlspecialchars($error_message); ?></div>
    <?php elseif($car): ?>

        <h2>Place Order: <?= htmlspecialchars($car['title']); ?></h2>
        <p><strong>Make:</strong> <?= htmlspecialchars($car['make']); ?> | <strong>Model:</strong> <?= htmlspecialchars($car['model']); ?> | <strong>Year:</strong> <?= $car['year']; ?></p>
        <p><strong>Price:</strong> $<?= $car['price']; ?></p>

        <?php if($success_message): ?>
            <div class="message success"><?= htmlspecialchars($success_message); ?></div>
        <?php else: ?>
            <form method="POST">
                Quantity: <input type="number" name="quantity" value="1" min="1"><br><br>
                <input type="hidden" name="car_id" value="<?= $car_id; ?>">
                <button type="submit" name="place_order">Place Order</button>
            </form>
        <?php endif; ?>

    <?php endif; ?>
</div>

</body>
</html>
