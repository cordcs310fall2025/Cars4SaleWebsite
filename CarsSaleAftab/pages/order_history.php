<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user orders
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
<title>Order History</title>
<style>
body { font-family: Arial; background:#f4f6fb; }
.order-table { width:90%; margin:20px auto; border-collapse:collapse; background:#fff; box-shadow:0 4px 10px rgba(0,0,0,0.1); }
.order-table th, .order-table td { padding:12px; border:1px solid #ddd; }
.order-table th { background:#f2f2f2;
