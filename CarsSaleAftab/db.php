<?php
// Database configuration
$servername = "localhost";
$username = "root";        // Default XAMPP username
$password = "";            // Default XAMPP password is usually empty
$dbname = "CarSaleAftab"; // Make sure this database exists in phpMyAdmin

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Optional: set character set to UTF-8 for proper encoding
$conn->set_charset("utf8");

// Uncomment the line below to verify connection (for debugging purposes)
// echo "Connected to database successfully!";
?>
