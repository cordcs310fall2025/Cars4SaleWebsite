<?php
session_start();
require 'db.php';

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Validate car_id
if (!isset($_GET['car_id']) || !is_numeric($_GET['car_id'])) {
    header("Location: home.php");
    exit;
}

$car_id = (int) $_GET['car_id'];

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add or increment car
if (isset($_SESSION['cart'][$car_id])) {
    $_SESSION['cart'][$car_id]++;
} else {
    $_SESSION['cart'][$car_id] = 1;
}

// Redirect to cart page
header("Location: cart.php");
exit;
