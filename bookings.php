<?php
session_start();
include_once(__DIR__ . "/connection.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: loging.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Create bookings table if it doesn't exist with correct structure
$create_bookings_table = "CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    vehicle_id INT NOT NULL,
    pickup_date DATE NOT NULL,
    return_date DATE NOT NULL,
    total_days INT DEFAULT 1,
    pickup_location VARCHAR(255),
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'active', 'completed', 'cancelled') DEFAULT 'pending',
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    payment_method VARCHAR(50) DEFAULT 'esewa',
    transaction_id VARCHAR(100)
)";
$conn->query($create_bookings_table);

// Handle booking cancellation
if (isset($_GET['cancel']) && isset($_GET['booking_id'])) {
    $booking_id = intval($_GET['cancel']);
    
    $check_sql = "SELECT id, status FROM bookings WHERE id = ? AND user_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    
    if ($check_stmt) {
        $check_stmt->bind_param("ii", $booking_id, $user_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $booking = $check_result->fetch_assoc();
            if ($booking['status'] == 'pending' || $booking['status'] == 'confirmed') {
                $cancel_sql = "UPDATE bookings SET status = 'cancelled' WHERE id = ?";
                $cancel_stmt = $conn->prepare($cancel_sql);
                if ($cancel_stmt) {
                    $cancel_stmt->bind_param("i", $booking_id);
                    if ($cancel_stmt->execute()) {
                        $success = "Booking cancelled successfully!";
                    } else {
                        $error = "Failed to cancel booking.";
                    }
                    $cancel_stmt->close();
                }
            } else {
                $error = "This booking cannot be cancelled.";
            }
        } else {
            $error = "Booking not found.";
        }
        $check_stmt->close();
    }
}

// Fetch user bookings - using 'booking_date' instead of 'created_at'
$sql = "SELECT b.*, 
        v.brand, 
        v.model,
        v.type as vehicle_type,
        v.image_url as vehicle_image,
        v.daily_rate
        FROM bookings b
        LEFT JOIN vehicles v ON b.vehicle_id = v.id
        WHERE b.user_id = ?
        ORDER BY b.booking_date DESC";

$stmt = $conn->prepare($sql);
$bookings = [];

if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $bookings = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    $error = "Could not fetch bookings: " . $conn->error;
}

// Calculate statistics
$total_bookings = count($bookings);
$active_bookings = 0;
$completed_bookings = 0;
$cancelled_bookings = 0;
$total_spent = 0;

foreach ($bookings as $booking) {
    switch ($booking['status']) {
        case 'pending':
        case 'confirmed':
        case 'active':
            $active_bookings++;
            break;
        case 'completed':
            $completed_bookings++;
            $total_spent += floatval($booking['total_amount']);
            break;
        case 'cancelled':
            $cancelled_bookings++;
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - Travel_X</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 80px 20px 40px;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            padding: 1rem 2rem;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        .logo h2 {
            font-size: 1.8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #1f6e43, #2b9b5e);
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
        }

        .nav-links {
            display: flex;
            gap: 1.5rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .nav-links a {
            text-decoration: none;
            font-weight: 500;
            color: #2c3e44;
            transition: 0.2s;
        }

        .nav-links a:hover, .nav-links a.active {
            color: #1f6e43;
        }

        .logout-btn {
            background: #e74c3c;
            color: white;
            padding: 0.5rem 1.2rem;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 600;
        }

        .bookings-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .page-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .page-header h1 {
            font-size: 2.5rem;
            color: white;
            margin-bottom: 0.5rem;
        }

        .page-header p {
            color: rgba(255,255,255,0.8);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 1rem;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }

        .stat-card i {
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        .stat-card .stat-number {
            font-size: 1.4rem;
            font-weight: 700;
            color: #333;
        }

        .stat-card .stat-label {
            color: #666;
            font-size: 0.75rem;
        }

        .stat-card.total i { color: #3498db; }
        .stat-card.active i { color: #27ae60; }
        .stat-card.completed i { color: #2ecc71; }
        .stat-card.cancelled i { color: #e74c3c; }
        .stat-card.spent i { color: #f39c12; }

        .alert {
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .booking-card {
            background: white;
            border-radius: 16px;
            margin-bottom: 1rem;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .booking-card:hover {
            transform: translateY(-2px);
        }

        .booking-header {
            background: #f8fafc;
            padding: 0.8rem 1.2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .booking-id {
            font-weight: 600;
            color: #1f6e43;
            font-size: 0.85rem;
        }

        .booking-date {
            font-size: 0.75rem;
            color: #888;
        }

        .status-badge {
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .status-pending { background: #fef3c7; color: #d97706; }
        .status-confirmed { background: #dbeafe; color: #2563eb; }
        .status-active { background: #d1fae5; color: #059669; }
        .status-completed { background: #d4edda; color: #155724; }
        .status-cancelled { background: #fee2e2; color: #dc2626; }

        .booking-body {
            display: flex;
            padding: 1.2rem;
            gap: 1rem;
        }

        .vehicle-icon {
            width: 80px;
            height: 80px;
            background: #f1f5f9;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .vehicle-icon i {
            font-size: 2rem;
            color: #1f6e43;
        }

        .booking-details {
            flex: 1;
        }

        .vehicle-name {
            font-size: 1rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.25rem;
        }

        .vehicle-type {
            font-size: 0.7rem;
            color: #1f6e43;
            margin-bottom: 0.5rem;
        }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 0.4rem;
            font-size: 0.75rem;
        }

        .detail-item i {
            width: 18px;
            color: #1f6e43;
        }

        .booking-footer {
            background: #f8fafc;
            padding: 0.8rem 1.2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
            border-top: 1px solid #e2e8f0;
        }

        .total-amount {
            font-weight: 700;
            color: #1f6e43;
            font-size: 0.9rem;
        }

        .btn {
            padding: 0.4rem 1rem;
            border: none;
            border-radius: 8px;
            font-size: 0.7rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-primary {
            background: #1f6e43;
            color: white;
        }

        .btn-danger {
            background: #e74c3c;
            color: white;
        }

        .empty-state {
            background: white;
            border-radius: 16px;
            padding: 3rem;
            text-align: center;
        }

        .empty-state i {
            font-size: 3rem;
            color: #cbd5e1;
            margin-bottom: 1rem;
        }

        .empty-state h3 {
            margin-bottom: 0.5rem;
            color: #333;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 2000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            max-width: 350px;
            width: 90%;
            text-align: center;
        }

        .modal-buttons {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
            margin-top: 1rem;
        }

        @media (max-width: 768px) {
            body {
                padding: 70px 15px 30px;
            }
            .navbar {
                flex-direction: column;
            }
            .booking-body {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
            .details-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="logo">
        <h2>Travel <span style="color:#eab308;">X</span></h2>
    </div>
    <div class="nav-links">
        <a href="homepage.php"><i class="fas fa-home"></i> Home</a>
        <a href="vehicles.php"><i class="fas fa-car"></i> Vehicles</a>
        <a href="profile.php"><i class="fas fa-user-circle"></i> My Profile</a>
        <a href="bookings.php" class="active"><i class="fas fa-bookmark"></i> My Bookings</a>
        <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="bookings-container">
    <div class="page-header">
        <h1><i class="fas fa-bookmark"></i> My Bookings</h1>
        <p>View and manage all your vehicle rental bookings</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card total">
            <i class="fas fa-calendar-alt"></i>
            <div class="stat-number"><?php echo $total_bookings; ?></div>
            <div class="stat-label">Total Bookings</div>
        </div>
        <div class="stat-card active">
            <i class="fas fa-play-circle"></i>
            <div class="stat-number"><?php echo $active_bookings; ?></div>
            <div class="stat-label">Active</div>
        </div>
        <div class="stat-card completed">
            <i class="fas fa-check-circle"></i>
            <div class="stat-number"><?php echo $completed_bookings; ?></div>
            <div class="stat-label">Completed</div>
        </div>
        <div class="stat-card cancelled">
            <i class="fas fa-times-circle"></i>
            <div class="stat-number"><?php echo $cancelled_bookings; ?></div>
            <div class="stat-label">Cancelled</div>
        </div>
        <div class="stat-card spent">
            <i class="fas fa-rupee-sign"></i>
            <div class="stat-number">NPR <?php echo number_format($total_spent, 0); ?></div>
            <div class="stat-label">Total Spent</div>
        </div>
    </div>

    <?php if ($success): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <div><?php echo $success; ?></div>
    </div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-triangle"></i>
        <div><?php echo $error; ?></div>
    </div>
    <?php endif; ?>

    <?php if (count($bookings) > 0): ?>
        <?php foreach ($bookings as $booking): ?>
        <div class="booking-card">
            <div class="booking-header">
                <span class="booking-id"><i class="fas fa-hashtag"></i> Booking #<?php echo str_pad($booking['id'], 6, '0', STR_PAD_LEFT); ?></span>
                <span class="booking-date"><i class="fas fa-clock"></i> <?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></span>
                <span class="status-badge status-<?php echo $booking['status']; ?>">
                    <?php echo ucfirst($booking['status']); ?>
                </span>
            </div>
            <div class="booking-body">
                <div class="vehicle-icon">
                    <i class="fas fa-car"></i>
                </div>
                <div class="booking-details">
                    <div class="vehicle-name"><?php echo htmlspecialchars($booking['brand'] . ' ' . $booking['model']); ?></div>
                    <div class="vehicle-type"><?php echo htmlspecialchars($booking['vehicle_type']); ?></div>
                    <div class="details-grid">
                        <div class="detail-item"><i class="fas fa-calendar"></i> Pickup: <?php echo date('M d, Y', strtotime($booking['pickup_date'])); ?></div>
                        <div class="detail-item"><i class="fas fa-calendar"></i> Return: <?php echo date('M d, Y', strtotime($booking['return_date'])); ?></div>
                        <div class="detail-item"><i class="fas fa-clock"></i> Duration: <?php echo $booking['total_days']; ?> days</div>
                        <div class="detail-item"><i class="fas fa-tag"></i> Rate: NPR <?php echo number_format($booking['daily_rate'] ?? 0, 0); ?>/day</div>
                    </div>
                </div>
            </div>
            <div class="booking-footer">
                <div class="total-amount">Total: NPR <?php echo number_format($booking['total_amount'], 0); ?></div>
                <?php if ($booking['status'] == 'pending' || $booking['status'] == 'confirmed'): ?>
                    <button class="btn btn-danger" onclick="openCancelModal(<?php echo $booking['id']; ?>)">
                        <i class="fas fa-times"></i> Cancel Booking
                    </button>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-bookmark"></i>
            <h3>No Bookings Yet</h3>
            <p>You haven't made any vehicle rentals yet. Start your journey with Travel_X!</p>
            <a href="vehicles.php" class="btn btn-primary"><i class="fas fa-car"></i> Browse Vehicles</a>
        </div>
    <?php endif; ?>
</div>

<div id="cancelModal" class="modal">
    <div class="modal-content">
        <i class="fas fa-exclamation-triangle" style="font-size: 2rem; color: #e74c3c;"></i>
        <h3>Cancel Booking?</h3>
        <p>Are you sure you want to cancel this booking? This action cannot be undone.</p>
        <div class="modal-buttons">
            <button class="btn btn-primary" onclick="closeCancelModal()">No, Keep It</button>
            <a href="#" id="cancelLink" class="btn btn-danger">Yes, Cancel</a>
        </div>
    </div>
</div>

<script>
    function openCancelModal(bookingId) {
        const modal = document.getElementById('cancelModal');
        const cancelLink = document.getElementById('cancelLink');
        cancelLink.href = `?cancel=${bookingId}&booking_id=${bookingId}`;
        modal.classList.add('active');
    }
    
    function closeCancelModal() {
        const modal = document.getElementById('cancelModal');
        modal.classList.remove('active');
    }
    
    window.onclick = function(event) {
        const modal = document.getElementById('cancelModal');
        if (event.target === modal) {
            modal.classList.remove('active');
        }
    }
</script>

</body>
</html>