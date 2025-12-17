<?php
session_start();
require_once __DIR__ . '/../db.php';
if (!isset($_SESSION['admin_id']) || ($_SESSION['admin_role'] ?? '') !== 'super') { header("Location: index.php"); exit; }
$id = intval($_GET['id']);
$conn->query("DELETE FROM cars WHERE car_id={$id}");
header("Location: cars.php");
