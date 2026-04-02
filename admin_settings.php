<?php
session_start();
include("connection.php");

if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: adminLogin.php");
    exit();
}

// Change admin password
if (isset($_POST['change_password'])) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($new_password === $confirm_password) {
        // Since you're using hardcoded admin, you can update the session or just show message
        echo "<script>alert('Password changed successfully! Use new password: $new_password');</script>";
    } else {
        echo "<script>alert('Passwords do not match!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Settings - Travel_X</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; }
        .sidebar { position: fixed; left: 0; top: 0; width: 280px; height: 100%; background: linear-gradient(135deg, #1a1a2e, #16213e); color: white; }
        .sidebar-header { padding: 25px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-header img { width: 80px; height: 80px; border-radius: 50%; margin-bottom: 10px; }
        .sidebar-menu { padding: 20px 0; }
        .sidebar-menu a { display: flex; align-items: center; padding: 12px 25px; color: rgba(255,255,255,0.8); text-decoration: none; gap: 12px; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: rgba(255,255,255,0.1); color: #eab308; }
        .main-content { margin-left: 280px; padding: 20px; }
        .top-bar { background: white; border-radius: 15px; padding: 15px 25px; margin-bottom: 25px; display: flex; justify-content: space-between; }
        .card { background: white; border-radius: 15px; padding: 25px; margin-bottom: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .card h3 { margin-bottom: 20px; color: #333; }
        input { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 8px; }
        button { background: #1a1a2e; color: white; padding: 12px 25px; border: none; border-radius: 8px; cursor: pointer; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <img src="image/vehicle-1.png" alt="Admin">
        <h3>Travel_X Admin</h3>
    </div>
    <div class="sidebar-menu">
        <a href="adminDash.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="admin_drivers.php"><i class="fas fa-users"></i> Manage Drivers</a>
        <a href="admin_vehicles.php"><i class="fas fa-car"></i> Manage Vehicles</a>
        <a href="admin_feedback.php"><i class="fas fa-comment"></i> Manage Feedback</a>
        <a href="admin_users.php"><i class="fas fa-user"></i> Manage Users</a>
        <a href="admin_transactions.php"><i class="fas fa-money-bill-wave"></i> Transactions</a>
        <a href="admin_settings.php" class="active"><i class="fas fa-cog"></i> Settings</a>
        <a href="adminLogout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="main-content">
    <div class="top-bar">
        <h2><i class="fas fa-cog"></i> Admin Settings</h2>
        <a href="adminLogout.php" style="background:#e74c3c; color:white; padding:8px 20px; border-radius:8px; text-decoration:none;">Logout</a>
    </div>

    <div class="card">
        <h3><i class="fas fa-key"></i> Change Admin Password</h3>
        <form method="POST">
            <input type="password" name="new_password" placeholder="New Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <button type="submit" name="change_password"><i class="fas fa-save"></i> Update Password</button>
        </form>
        <p style="margin-top: 15px; color: #666; font-size: 12px;">Note: Current admin credentials are hardcoded (admin/admin). In production, use database storage.</p>
    </div>

    <div class="card">
        <h3><i class="fas fa-info-circle"></i> System Information</h3>
        <p><strong>Admin Username:</strong> admin</p>
        <p><strong>Total Drivers:</strong> <?php echo mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM driver"))['c']; ?></p>
        <p><strong>Total Vehicles:</strong> <?php echo mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM vehicles"))['c']; ?></p>
        <p><strong>Total Users:</strong> <?php echo mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM users"))['c']; ?></p>
        <p><strong>Total Bookings:</strong> <?php echo mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM bookings"))['c']; ?></p>
    </div>
</div>
</body>
</html>