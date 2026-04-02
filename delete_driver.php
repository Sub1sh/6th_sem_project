<?php
session_start();
include("connection.php");

if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: adminLogin.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    mysqli_query($conn, "DELETE FROM driver WHERE id = $id");
    echo "<script>alert('Driver deleted successfully!'); window.location.href='admin_drivers.php';</script>";
} else {
    header("Location: admin_drivers.php");
}
?>