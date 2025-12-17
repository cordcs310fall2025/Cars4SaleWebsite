if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {

    $car_id = intval($_POST['car_id']);
    $quantity = intval($_POST['quantity']);

    if ($car_id <= 0 || $quantity <= 0) {
        die("Invalid order data.");
    }

    // Fetch car price securely
    $stmt = $conn->prepare("SELECT price FROM cars WHERE car_id = ?");
    $stmt->bind_param("i", $car_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $car = $result->fetch_assoc();

    if (!$car) {
        die("Car not found.");
    }

    $total_price = $car['price'] * $quantity;

    // Insert order
    $stmt = $conn->prepare("
        INSERT INTO orders (user_id, total_amount, status)
        VALUES (?, ?, 'completed')
    ");
    $stmt->bind_param("id", $user_id, $total_price);
    $stmt->execute();

    $order_id = $stmt->insert_id;

    // Insert order item
    $stmt = $conn->prepare("
        INSERT INTO order_items (order_id, car_id, quantity, price)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("iiid", $order_id, $car_id, $quantity, $car['price']);
    $stmt->execute();

    header("Location: receipt.php?order_id=$order_id");
    exit;
}
