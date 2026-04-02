<?php
session_start();
include("connection.php");

if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: adminLogin.php");
    exit();
}

$id = $_GET['id'];
$vehicle = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM vehicles WHERE id = $id"));

if (isset($_POST['update_vehicle'])) {
    $brand = $_POST['brand'];
    $model = $_POST['model'];
    $type = $_POST['type'];
    $year = $_POST['year'];
    $transmission = $_POST['transmission'];
    $fuel_type = $_POST['fuel_type'];
    $daily_rate = $_POST['daily_rate'];
    $status = $_POST['status'];
    
    mysqli_query($conn, "UPDATE vehicles SET brand='$brand', model='$model', type='$type', year='$year', transmission='$transmission', fuel_type='$fuel_type', daily_rate='$daily_rate', status='$status' WHERE id=$id");
    echo "<script>alert('Vehicle updated successfully!'); window.location.href='admin_vehicles.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Vehicle</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: Arial; background: #f0f2f5; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .edit-form { background: white; padding: 30px; border-radius: 15px; width: 600px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        h2 { margin-bottom: 20px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        input, select { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 8px; }
        button { background: #1a1a2e; color: white; padding: 12px 25px; border: none; border-radius: 8px; cursor: pointer; }
        .back { display: inline-block; margin-top: 15px; color: #666; text-decoration: none; }
    </style>
</head>
<body>
    <div class="edit-form">
        <h2><i class="fas fa-edit"></i> Edit Vehicle</h2>
        <form method="POST">
            <div class="form-row">
                <input type="text" name="brand" value="<?php echo $vehicle['brand']; ?>" required>
                <input type="text" name="model" value="<?php echo $vehicle['model']; ?>" required>
            </div>
            <div class="form-row">
                <input type="text" name="type" value="<?php echo $vehicle['type']; ?>" required>
                <input type="number" name="year" value="<?php echo $vehicle['year']; ?>" required>
            </div>
            <div class="form-row">
                <select name="transmission" required>
                    <option value="Automatic" <?php echo $vehicle['transmission'] == 'Automatic' ? 'selected' : ''; ?>>Automatic</option>
                    <option value="Manual" <?php echo $vehicle['transmission'] == 'Manual' ? 'selected' : ''; ?>>Manual</option>
                </select>
                <select name="fuel_type" required>
                    <option value="Petrol" <?php echo $vehicle['fuel_type'] == 'Petrol' ? 'selected' : ''; ?>>Petrol</option>
                    <option value="Diesel" <?php echo $vehicle['fuel_type'] == 'Diesel' ? 'selected' : ''; ?>>Diesel</option>
                    <option value="Electric" <?php echo $vehicle['fuel_type'] == 'Electric' ? 'selected' : ''; ?>>Electric</option>
                </select>
            </div>
            <div class="form-row">
                <input type="number" name="daily_rate" value="<?php echo $vehicle['daily_rate']; ?>" required>
                <select name="status" required>
                    <option value="available" <?php echo $vehicle['status'] == 'available' ? 'selected' : ''; ?>>Available</option>
                    <option value="rented" <?php echo $vehicle['status'] == 'rented' ? 'selected' : ''; ?>>Rented</option>
                    <option value="maintenance" <?php echo $vehicle['status'] == 'maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                </select>
            </div>
            <button type="submit" name="update_vehicle"><i class="fas fa-save"></i> Update Vehicle</button>
        </form>
        <a href="admin_vehicles.php" class="back"><i class="fas fa-arrow-left"></i> Back to Vehicles</a>
    </div>
</body>
</html>