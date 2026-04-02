<?php
session_start();
include("connection.php");

// Redirect to admin login if not logged in
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: adminLogin.php");
    exit();
}

// Create missing tables if they don't exist
// Create driver table
$conn->query("CREATE TABLE IF NOT EXISTS driver (
    id INT AUTO_INCREMENT PRIMARY KEY,
    driver_name VARCHAR(100) NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    address VARCHAR(255),
    anticipate_amount DECIMAL(10,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Create feedback table
$conn->query("CREATE TABLE IF NOT EXISTS feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    rating INT DEFAULT 5,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Get statistics with error handling
$total_drivers = 0;
$driver_result = $conn->query("SELECT COUNT(*) as count FROM driver");
if ($driver_result && $driver_result->num_rows > 0) {
    $total_drivers = mysqli_fetch_assoc($driver_result)['count'];
}

$total_vehicles = 0;
$vehicle_result = $conn->query("SELECT COUNT(*) as count FROM vehicles");
if ($vehicle_result && $vehicle_result->num_rows > 0) {
    $total_vehicles = mysqli_fetch_assoc($vehicle_result)['count'];
}

$total_bookings = 0;
$booking_result = $conn->query("SELECT COUNT(*) as count FROM bookings");
if ($booking_result && $booking_result->num_rows > 0) {
    $total_bookings = mysqli_fetch_assoc($booking_result)['count'];
}

$total_feedback = 0;
$feedback_result = $conn->query("SELECT COUNT(*) as count FROM feedback");
if ($feedback_result && $feedback_result->num_rows > 0) {
    $total_feedback = mysqli_fetch_assoc($feedback_result)['count'];
}

$total_users = 0;
$user_result = $conn->query("SELECT COUNT(*) as count FROM users");
if ($user_result && $user_result->num_rows > 0) {
    $total_users = mysqli_fetch_assoc($user_result)['count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Travel_X</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 280px;
            height: 100%;
            background: linear-gradient(135deg, #1a1a2e, #16213e);
            color: white;
            transition: 0.3s;
            z-index: 100;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 25px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-header img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
            border: 3px solid #eab308;
        }

        .sidebar-header h3 {
            font-size: 1.2rem;
            margin-bottom: 5px;
        }

        .sidebar-header p {
            font-size: 0.8rem;
            opacity: 0.7;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .sidebar-menu .menu-item {
            padding: 12px 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: 0.3s;
            border-left: 3px solid transparent;
        }

        .sidebar-menu .menu-item:hover,
        .sidebar-menu .menu-item.active {
            background: rgba(255,255,255,0.1);
            border-left-color: #eab308;
            color: #eab308;
        }

        .sidebar-menu .menu-item i {
            width: 24px;
            font-size: 1.1rem;
        }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            padding: 20px;
        }

        /* Top Bar */
        .top-bar {
            background: white;
            border-radius: 15px;
            padding: 15px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .page-title h2 {
            font-size: 1.5rem;
            color: #333;
        }

        .admin-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .logout-btn {
            background: #e74c3c;
            color: white;
            padding: 8px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
        }

        .logout-btn:hover {
            background: #c0392b;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .stat-icon {
            width: 55px;
            height: 55px;
            background: linear-gradient(135deg, #1a1a2e, #16213e);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-icon i {
            font-size: 1.5rem;
            color: #eab308;
        }

        .stat-info h3 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #333;
        }

        .stat-info p {
            color: #666;
            font-size: 0.85rem;
        }

        /* Welcome Section */
        .welcome-section {
            background: linear-gradient(135deg, #1a1a2e, #16213e);
            border-radius: 15px;
            padding: 30px;
            color: white;
            margin-bottom: 30px;
        }

        .welcome-section h2 {
            font-size: 1.8rem;
            margin-bottom: 10px;
        }

        .welcome-section p {
            opacity: 0.8;
        }

        @media (max-width: 768px) {
            .sidebar {
                left: -280px;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <img src="image/vehicle-1.png" alt="Admin">
        <h3>Travel_X Admin</h3>
        <p><?php echo $_SESSION['username']; ?></p>
    </div>
    <div class="sidebar-menu">
        <a href="adminDash.php" class="menu-item active">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="admin_drivers.php" class="menu-item">
            <i class="fas fa-users"></i> Manage Drivers
        </a>
        <a href="admin_vehicles.php" class="menu-item">
            <i class="fas fa-car"></i> Manage Vehicles
        </a>
        <a href="admin_bookings.php" class="menu-item">
            <i class="fas fa-bookmark"></i> Manage Bookings
        </a>
        <a href="admin_feedback.php" class="menu-item">
            <i class="fas fa-comment"></i> Manage Feedback
        </a>
        <a href="admin_users.php" class="menu-item">
            <i class="fas fa-user"></i> Manage Users
        </a>
    
        <a href="admin_settings.php" class="menu-item">
            <i class="fas fa-cog"></i> Settings
        </a>
        <a href="adminLogout.php" class="menu-item">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</div>

<div class="main-content">
    <div class="top-bar">
        <div class="page-title">
            <h2><i class="fas fa-tachometer-alt"></i> Dashboard</h2>
        </div>
        <div class="admin-info">
            <span><i class="fas fa-user-shield"></i> Admin</span>
            <a href="adminLogout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <div class="welcome-section">
        <h2>Welcome back, Admin!</h2>
        <p>Here's what's happening with your vehicle rental business today.</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div class="stat-info">
                <h3><?php echo $total_drivers; ?></h3>
                <p>Total Drivers</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-car"></i></div>
            <div class="stat-info">
                <h3><?php echo $total_vehicles; ?></h3>
                <p>Total Vehicles</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-bookmark"></i></div>
            <div class="stat-info">
                <h3><?php echo $total_bookings; ?></h3>
                <p>Total Bookings</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-comment"></i></div>
            <div class="stat-info">
                <h3><?php echo $total_feedback; ?></h3>
                <p>Feedbacks</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-user"></i></div>
            <div class="stat-info">
                <h3><?php echo $total_users; ?></h3>
                <p>Registered Users</p>
            </div>
        </div>
    </div>
</div>

</body>
</html>