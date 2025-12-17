<?php
session_start();
require 'db.php'; // Database connection

// Handle login form submission
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            header("Location: http://localhost/CarsSaleAftab/home.php");
            exit;
        } else {
            $login_error = "Incorrect password.";
        }
    } else {
        $login_error = "Email not found.";
    }
    $stmt->close();
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}
?>

// Fetch all cars
$result = $conn->query("SELECT * FROM cars ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CarsSaleAftab</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Montserrat:wght@600&display=swap" rel="stylesheet">
    <style>
        /* Reset */
        * { margin:0; padding:0; box-sizing:border-box; }

        body { font-family: 'Roboto', sans-serif; background: #f0f2f5; }

        /* Header */
        header { background: linear-gradient(90deg,#1c1c1c,#111); color:#fff; padding:20px 40px; text-align:center; }
        header h1 { font-family:'Montserrat', sans-serif; font-weight:700; font-size:28px; }
        nav { margin-top:10px; }
        nav a { color:#fff; margin:0 10px; text-decoration:none; font-weight:600; transition: color 0.3s; }
        nav a:hover { color:#FFD700; }

        .container { width:90%; margin:30px auto; }

        /* Login Form */
        .login-form { background:#fff; padding:20px; border-radius:10px; box-shadow:0 4px 15px rgba(0,0,0,0.1); max-width:400px; margin:20px auto; }
        .login-form h3 { margin-bottom:15px; font-family:'Montserrat', sans-serif; font-weight:700; color:#1c1c1c; }
        .login-form input { width:100%; padding:10px; margin:8px 0; border-radius:5px; border:1px solid #ccc; }
        .login-form button { padding:10px 15px; background:#FFD700; border:none; border-radius:5px; cursor:pointer; font-weight:600; width:100%; transition:0.3s; }
        .login-form button:hover { background:#e0b800; }

        /* Cars Grid */
        .cars { display:grid; grid-template-columns: repeat(auto-fill,minmax(280px,1fr)); gap:30px; margin-top:30px; }

        .car { background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 8px 20px rgba(0,0,0,0.15); display:flex; flex-direction:column; transition:transform 0.3s; }
        .car:hover { transform: translateY(-8px); }
        .car img { width:100%; height:180px; object-fit:cover; transition:transform 0.3s; }
        .car img:hover { transform: scale(1.05); }
        .car-details { padding:15px; flex:1; display:flex; flex-direction:column; }
        .car-details h2 { font-family:'Montserrat', sans-serif; font-weight:700; margin-bottom:8px; }
        .car-details p { margin:5px 0; color:#555; }
        .price { font-weight:700; color:#1c1c1c; margin-top:10px; font-size:18px; }
        .order-btn { text-align:center; padding:10px 12px; border-radius:8px; text-decoration:none; font-weight:600; display:inline-block; margin-top:auto; background:#28a745; color:#fff; transition:0.3s; }
        .order-btn:hover { background:#218838; }

        @media(max-width:768px){ .cars { grid-template-columns: repeat(auto-fill,minmax(220px,1fr)); } }
        @media(max-width:480px){ header { padding:15px; } }
    </style>
</head>
<body>

<header>
    <h1>CarsSaleAftab</h1>
    <p>
        <?php if(isset($_SESSION['username'])): ?>
            Welcome, <?= htmlspecialchars($_SESSION['username']); ?> | 
            <a href="?logout=1">Logout</a> | 
            <a href="add_car.php">Add Car</a> | 
            <a href="cart.php">My Cart</a>
        <?php else: ?>
            <a href="#login">Login</a> | <a href="register.php">Register</a>
        <?php endif; ?>
    </p>
</header>

<div class="container">

    <!-- Login Form -->
    <?php if(!isset($_SESSION['user_id'])): ?>
        <div class="login-form" id="login">
            <h3>Login</h3>
            <?php if(isset($login_error)) echo "<p style='color:red;'>$login_error</p>"; ?>
            <form method="POST">
                Email: <input type="email" name="email" required><br>
                Password: <input type="password" name="password" required><br>
                <button type="submit" name="login">Login</button>
            </form>
        </div>
    <?php endif; ?>

    <!-- Car Listings -->
    <div class="cars">
    <?php
    if ($result->num_rows > 0) {
        while ($car = $result->fetch_assoc()) {
            $img = !empty($car['image']) ? $car['image'] : 'https://via.placeholder.com/280x180?text=Car';
            echo '<div class="car">';
            echo '<img src="'.$img.'" alt="'.htmlspecialchars($car['title']).'">';
            echo '<div class="car-details">';
            echo '<h2>'.htmlspecialchars($car['title']).' ('.$car['year'].')</h2>';
            echo '<p><strong>Make:</strong> '.htmlspecialchars($car['make']).' | <strong>Model:</strong> '.htmlspecialchars($car['model']).'</p>';
            echo '<p class="price">$'.$car['price'].'</p>';
            echo '<p>'.substr(htmlspecialchars($car['description']),0,100).'...</p>';

            if(isset($_SESSION['user_id'])) {
                echo '<a class="order-btn" href="create_order.php?user_id='.$_SESSION['user_id'].'&car_id='.$car['car_id'].'">Place Order</a>';
            } else {
                echo '<p><em>Login to place an order.</em></p>';
            }

            echo '</div></div>';
        }
    } else {
        echo '<p>No cars available at the moment. Check back soon!</p>';
    }
    ?>
    </div>

</div>

</body>
</html>