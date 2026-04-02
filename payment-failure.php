<?php
session_start();
include_once(__DIR__ . "/connection.php");

if (isset($_SESSION['current_booking_id'])) {
    $booking_id = $_SESSION['current_booking_id'];
    
    $update_sql = "UPDATE bookings SET status = 'cancelled' WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    
    if ($update_stmt) {
        $update_stmt->bind_param("i", $booking_id);
        $update_stmt->execute();
        $update_stmt->close();
    }
    
    unset($_SESSION['current_booking_id']);
    unset($_SESSION['booking_amount']);
    
    $_SESSION['error'] = "❌ Payment failed. Please try again.";
    header("Location: vehicles.php");
    exit();
} else {
    header("Location: vehicles.php");
    exit();
}
?>