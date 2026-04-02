<?php
session_start();
include("connection.php");

if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: adminLogin.php");
    exit();
}

// Handle Add Vehicle
if (isset($_POST['add_vehicle'])) {
    $brand = $_POST['brand'];
    $model = $_POST['model'];
    $type = $_POST['type'];
    $year = $_POST['year'];
    $transmission = $_POST['transmission'];
    $fuel_type = $_POST['fuel_type'];
    $daily_rate = $_POST['daily_rate'];
    $status = $_POST['status'];
    
    $insert = "INSERT INTO vehicles (brand, model, type, year, transmission, fuel_type, daily_rate, status) 
               VALUES ('$brand', '$model', '$type', '$year', '$transmission', '$fuel_type', '$daily_rate', '$status')";
    mysqli_query($conn, $insert);
    echo "<script>alert('Vehicle added successfully!'); window.location.href='admin_vehicles.php';</script>";
}

$vehicles = mysqli_query($conn, "SELECT * FROM vehicles ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Vehicles - Travel_X Admin</title>
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
        input, select { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 8px; }
        button { background: #1a1a2e; color: white; padding: 12px 25px; border: none; border-radius: 8px; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; }
        .btn-delete { background: #e74c3c; padding: 5px 12px; border-radius: 5px; color: white; text-decoration: none; }
        .btn-edit { background: #3498db; padding: 5px 12px; border-radius: 5px; color: white; text-decoration: none; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; }
        .status-badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; }
        .status-available { background: #d4edda; color: #155724; }
        .status-rented { background: #fee2e2; color: #dc2626; }
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
        <a href="admin_vehicles.php" class="active"><i class="fas fa-car"></i> Manage Vehicles</a>
        <a href="admin_feedback.php"><i class="fas fa-comment"></i> Manage Feedback</a>
        <a href="admin_users.php"><i class="fas fa-user"></i> Manage Users</a>
        <a href="admin_transactions.php"><i class="fas fa-money-bill-wave"></i> Transactions</a>
        <a href="adminLogout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="main-content">
    <div class="top-bar">
        <h2><i class="fas fa-car"></i> Manage Vehicles</h2>
        <a href="adminLogout.php" style="background:#e74c3c; color:white; padding:8px 20px; border-radius:8px; text-decoration:none;">Logout</a>
    </div>

    <!-- Add Vehicle Form -->
    <div class="card">
        <h3><i class="fas fa-plus-circle"></i> Add New Vehicle</h3>
        <form method="POST">
            <div class="form-row">
                <input type="text" name="brand" placeholder="Brand" required>
                <input type="text" name="model" placeholder="Model" required>
                <input type="text" name="type" placeholder="Type (e.g., Sedan, SUV, Truck)" required>
            </div>
            <div class="form-row">
                <input type="number" name="year" placeholder="Year" required>
                <select name="transmission" required>
                    <option value="">Transmission</option>
                    <option value="Automatic">Automatic</option>
                    <option value="Manual">Manual</option>
                </select>
                <select name="fuel_type" required>
                    <option value="">Fuel Type</option>
                    <option value="Petrol">Petrol</option>
                    <option value="Diesel">Diesel</option>
                    <option value="Electric">Electric</option>
                </select>
            </div>
            <div class="form-row">
                <input type="number" name="daily_rate" placeholder="Daily Rate (NPR)" required>
                <select name="status" required>
                    <option value="available">Available</option>
                    <option value="rented">Rented</option>
                    <option value="maintenance">Maintenance</option>
                </select>
            </div>
            <button type="submit" name="add_vehicle"><i class="fas fa-save"></i> Add Vehicle</button>
        </form>
    </div>

    <!-- Vehicles List -->
    <div class="card">
        <h3><i class="fas fa-list"></i> All Vehicles</h3>
        <table>
            <thead>
                <tr><th>ID</th><th>Brand</th><th>Model</th><th>Type</th><th>Year</th><th>Transmission</th><th>Fuel</th><th>Rate/Day</th><th>Status</th><th>Actions</th</tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($vehicles)): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['brand']; ?></td>
                    <td><?php echo $row['model']; ?></td>
                    <td><?php echo $row['type']; ?></td>
                    <td><?php echo $row['year']; ?></td>
                    <td><?php echo $row['transmission']; ?></td>
                    <td><?php echo $row['fuel_type']; ?></td>
                    <td>NPR <?php echo number_format($row['daily_rate'], 0); ?></td>
                    <td><span class="status-badge status-<?php echo $row['status']; ?>"><?php echo $row['status']; ?></span></td>
                    <td>
                        <a href="edit_vehicle.php?id=<?php echo $row['id']; ?>" class="btn-edit"><i class="fas fa-edit"></i> Edit</a>
                        <a href="delete_vehicle.php?id=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Delete this vehicle?')"><i class="fas fa-trash"></i> Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>