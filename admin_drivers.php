<?php
session_start();
include("connection.php");

if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: adminLogin.php");
    exit();
}

// Create driver table if not exists
$conn->query("CREATE TABLE IF NOT EXISTS driver (
    id INT AUTO_INCREMENT PRIMARY KEY,
    driver_name VARCHAR(100) NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    address VARCHAR(255),
    anticipate_amount DECIMAL(10,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Handle Add Driver
if (isset($_POST['add_driver'])) {
    $driver_name = mysqli_real_escape_string($conn, $_POST['driver_name']);
    $telephone = mysqli_real_escape_string($conn, $_POST['telephone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $anticipate_amount = floatval($_POST['anticipate_amount']);
    
    $insert = "INSERT INTO driver (driver_name, telephone, address, anticipate_amount) 
               VALUES ('$driver_name', '$telephone', '$address', '$anticipate_amount')";
    if (mysqli_query($conn, $insert)) {
        echo "<script>alert('Driver added successfully!'); window.location.href='admin_drivers.php';</script>";
    } else {
        echo "<script>alert('Error adding driver: " . mysqli_error($conn) . "');</script>";
    }
}

// Handle Delete Driver
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    if (mysqli_query($conn, "DELETE FROM driver WHERE id = $id")) {
        echo "<script>alert('Driver deleted successfully!'); window.location.href='admin_drivers.php';</script>";
    } else {
        echo "<script>alert('Error deleting driver');</script>";
    }
}

// Get all drivers
$drivers = mysqli_query($conn, "SELECT * FROM driver ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Drivers - Travel_X Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; }
        .sidebar { position: fixed; left: 0; top: 0; width: 280px; height: 100%; background: linear-gradient(135deg, #1a1a2e, #16213e); color: white; overflow-y: auto; }
        .sidebar-header { padding: 25px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-header img { width: 80px; height: 80px; border-radius: 50%; margin-bottom: 10px; }
        .sidebar-menu { padding: 20px 0; }
        .sidebar-menu a { display: flex; align-items: center; padding: 12px 25px; color: rgba(255,255,255,0.8); text-decoration: none; gap: 12px; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: rgba(255,255,255,0.1); color: #eab308; }
        .main-content { margin-left: 280px; padding: 20px; }
        .top-bar { background: white; border-radius: 15px; padding: 15px 25px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; }
        .card { background: white; border-radius: 15px; padding: 25px; margin-bottom: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .card h3 { margin-bottom: 20px; color: #333; }
        input, select { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 8px; }
        button { background: #1a1a2e; color: white; padding: 12px 25px; border: none; border-radius: 8px; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; }
        .btn-delete { background: #e74c3c; padding: 5px 12px; border-radius: 5px; color: white; text-decoration: none; font-size: 12px; }
        .btn-edit { background: #3498db; padding: 5px 12px; border-radius: 5px; color: white; text-decoration: none; font-size: 12px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .logout-btn { background: #e74c3c; color: white; padding: 8px 20px; border-radius: 8px; text-decoration: none; }
        @media (max-width: 768px) { .main-content { margin-left: 0; } .sidebar { left: -280px; } }
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
        <a href="admin_drivers.php" class="active"><i class="fas fa-users"></i> Manage Drivers</a>
        <a href="admin_vehicles.php"><i class="fas fa-car"></i> Manage Vehicles</a>
        <a href="admin_feedback.php"><i class="fas fa-comment"></i> Manage Feedback</a>
        <a href="admin_users.php"><i class="fas fa-user"></i> Manage Users</a>
        <a href="admin_transactions.php"><i class="fas fa-money-bill-wave"></i> Transactions</a>
        <a href="admin_settings.php"><i class="fas fa-cog"></i> Settings</a>
        <a href="adminLogout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="main-content">
    <div class="top-bar">
        <h2><i class="fas fa-users"></i> Manage Drivers</h2>
        <a href="adminLogout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <!-- Add Driver Form -->
    <div class="card">
        <h3><i class="fas fa-plus-circle"></i> Add New Driver</h3>
        <form method="POST">
            <div class="form-row">
                <input type="text" name="driver_name" placeholder="Driver Name" required>
                <input type="text" name="telephone" placeholder="Telephone" required>
            </div>
            <div class="form-row">
                <input type="text" name="address" placeholder="Address" required>
                <input type="number" name="anticipate_amount" placeholder="Anticipate Amount" required>
            </div>
            <button type="submit" name="add_driver"><i class="fas fa-save"></i> Add Driver</button>
        </form>
    </div>

    <!-- Drivers List -->
    <div class="card">
        <h3><i class="fas fa-list"></i> All Drivers</h3>
        <?php if ($drivers && mysqli_num_rows($drivers) > 0): ?>
        <table>
            <thead>
                <tr><th>ID</th><th>Driver Name</th><th>Telephone</th><th>Address</th><th>Amount</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($drivers)): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['driver_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['telephone']); ?></td>
                    <td><?php echo htmlspecialchars($row['address']); ?></td>
                    <td>NPR <?php echo number_format($row['anticipate_amount'], 0); ?></td>
                    <td>
                        <a href="edit_driver.php?id=<?php echo $row['id']; ?>" class="btn-edit"><i class="fas fa-edit"></i> Edit</a>
                        <a href="admin_drivers.php?delete_id=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Delete this driver?')"><i class="fas fa-trash"></i> Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p style="text-align:center; padding:20px;">No drivers found. Add your first driver above.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>