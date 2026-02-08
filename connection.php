<?php
// connection.php - Correct database connection
$servername = "localhost";
$username = "root";
$password = "";  // Empty for XAMPP
$dbname = "rental";  // Changed from 'travel_x' to 'rental'

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset to UTF-8
mysqli_set_charset($conn, "utf8");

// Optional: Uncomment for debugging
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
?>