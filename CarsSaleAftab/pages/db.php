<?php
$conn = new mysqli("localhost", "root", "", "CarSaleAftab");
if ($conn->connect_error) {
    die("DB Connection failed");
}
session_start();
?>