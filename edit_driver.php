<?php
session_start();
include("connection.php");

if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: adminLogin.php");
    exit();
}

$id = $_GET['id'];
$driver = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM driver WHERE id = $id"));

if (isset($_POST['update_driver'])) {
    $driver_name = $_POST['driver_name'];
    $telephone = $_POST['telephone'];
    $address = $_POST['address'];
    $anticipate_amount = $_POST['anticipate_amount'];
    
    mysqli_query($conn, "UPDATE driver SET driver_name='$driver_name', telephone='$telephone', address='$address', anticipate_amount='$anticipate_amount' WHERE id=$id");
    echo "<script>alert('Driver updated successfully!'); window.location.href='admin_drivers.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Driver</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: Arial; background: #f0f2f5; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .edit-form { background: white; padding: 30px; border-radius: 15px; width: 500px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        h2 { margin-bottom: 20px; }
        input { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 8px; }
        button { background: #1a1a2e; color: white; padding: 12px 25px; border: none; border-radius: 8px; cursor: pointer; }
        .back { display: inline-block; margin-top: 15px; color: #666; text-decoration: none; }
    </style>
</head>
<body>
    <div class="edit-form">
        <h2><i class="fas fa-edit"></i> Edit Driver</h2>
        <form method="POST">
            <input type="text" name="driver_name" value="<?php echo $driver['driver_name']; ?>" required>
            <input type="text" name="telephone" value="<?php echo $driver['telephone']; ?>" required>
            <input type="text" name="address" value="<?php echo $driver['address']; ?>" required>
            <input type="number" name="anticipate_amount" value="<?php echo $driver['anticipate_amount']; ?>" required>
            <button type="submit" name="update_driver"><i class="fas fa-save"></i> Update Driver</button>
        </form>
        <a href="admin_drivers.php" class="back"><i class="fas fa-arrow-left"></i> Back to Drivers</a>
    </div>
</body>
</html>