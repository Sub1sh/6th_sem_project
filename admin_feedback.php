<?php
session_start();
include("connection.php");

if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: adminLogin.php");
    exit();
}

// Create feedback table if not exists
$conn->query("CREATE TABLE IF NOT EXISTS feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    rating INT DEFAULT 5,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Handle Delete Feedback
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    mysqli_query($conn, "DELETE FROM feedback WHERE id = $id");
    echo "<script>alert('Feedback deleted successfully!'); window.location.href='admin_feedback.php';</script>";
}

$feedbacks = mysqli_query($conn, "SELECT * FROM feedback ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Feedback - Travel_X Admin</title>
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
        .card { background: white; border-radius: 15px; padding: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .card h3 { margin-bottom: 20px; color: #333; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; }
        .btn-delete { background: #e74c3c; padding: 5px 12px; border-radius: 5px; color: white; text-decoration: none; font-size: 12px; }
        .stars { color: #f39c12; }
        .feedback-message { max-width: 300px; word-wrap: break-word; }
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
        <a href="admin_drivers.php"><i class="fas fa-users"></i> Manage Drivers</a>
        <a href="admin_vehicles.php"><i class="fas fa-car"></i> Manage Vehicles</a>
        <a href="admin_feedback.php" class="active"><i class="fas fa-comment"></i> Manage Feedback</a>
        <a href="admin_users.php"><i class="fas fa-user"></i> Manage Users</a>
        <a href="admin_transactions.php"><i class="fas fa-money-bill-wave"></i> Transactions</a>
        <a href="admin_settings.php"><i class="fas fa-cog"></i> Settings</a>
        <a href="adminLogout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="main-content">
    <div class="top-bar">
        <h2><i class="fas fa-comment"></i> Customer Feedback</h2>
        <a href="adminLogout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="card">
        <h3><i class="fas fa-list"></i> All Feedbacks</h3>
        <?php if ($feedbacks && mysqli_num_rows($feedbacks) > 0): ?>
        <table>
            <thead>
                <tr><th>ID</th><th>Name</th><th>Email</th><th>Rating</th><th>Message</th><th>Date</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($feedbacks)): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td class="stars">
                        <?php 
                        $rating = $row['rating'] ?? 0;
                        for($i=1; $i<=5; $i++) {
                            if($i <= $rating) echo '<i class="fas fa-star"></i>';
                            else echo '<i class="far fa-star"></i>';
                        }
                        ?>
                    </td>
                    <td class="feedback-message"><?php echo htmlspecialchars(substr($row['message'], 0, 100)); ?>...</td>
                    <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                    <td>
                        <a href="admin_feedback.php?delete_id=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Delete this feedback?')"><i class="fas fa-trash"></i> Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p style="text-align:center; padding:20px;">No feedback yet. Customers will leave feedback here.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>