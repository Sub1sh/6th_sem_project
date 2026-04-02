<?php
session_start();
include("connection.php");

if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: adminLogin.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    mysqli_query($conn, "DELETE FROM vehicles WHERE id = $id");
    echo "<script>alert('Vehicle deleted successfully!'); window.location.href='admin_vehicles.php';</script>";
} else {
    header("Location: admin_vehicles.php");
}
?>