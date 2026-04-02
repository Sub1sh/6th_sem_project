<?php
session_start();
include_once(__DIR__ . "/connection.php");

if (isset($_SESSION['current_booking_id'])) {
    $booking_id = $_SESSION['current_booking_id'];
    
    $update_sql = "UPDATE bookings SET status = 'confirmed' WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    
    if ($update_stmt) {
        $update_stmt->bind_param("i", $booking_id);
        $update_stmt->execute();
        $update_stmt->close();
    }
    
    unset($_SESSION['current_booking_id']);
    unset($_SESSION['booking_amount']);
    
    $_SESSION['success'] = "✅ Payment successful! Your booking is confirmed.";
    header("Location: bookings.php");
    exit();
} else {
    header("Location: vehicles.php");
    exit();
}
?>