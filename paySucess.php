<?php
session_start();
require_once 'config/database.php';

if (!isset($_GET['booking_id']) || !isset($_SESSION['user_id'])) {
    header('Location: homepage.php');
    exit();
}

$booking_id = $_GET['booking_id'];
$amount = $_GET['amount'] ?? 0;

// Update booking status in database
$stmt = $pdo->prepare("UPDATE bookings SET 
    payment_status = 'paid',
    payment_method = ?,
    payment_date = NOW(),
    total_amount = ?
    WHERE id = ? AND user_id = ?");

$payment_method = $_GET['method'] ?? 'credit_card';
$stmt->execute([$payment_method, $amount, $booking_id, $_SESSION['user_id']]);

// Get booking details for confirmation
$stmt = $pdo->prepare("SELECT b.*, v.name as vehicle_name, v.image as vehicle_image 
                       FROM bookings b 
                       JOIN vehicles v ON b.vehicle_id = v.id 
                       WHERE b.id = ? AND b.user_id = ?");
$stmt->execute([$booking_id, $_SESSION['user_id']]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

// Send confirmation email (in production)
// sendConfirmationEmail($_SESSION['email'], $booking);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - Travel_X</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .success-container {
            background: white;
            border-radius: 20px;
            padding: 50px;
            text-align: center;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .success-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #28a745, #20c997);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: scaleIn 0.5s ease 0.2s both;
        }

        @keyframes scaleIn {
            from { transform: scale(0); }
            to { transform: scale(1); }
        }

        .success-icon i {
            font-size: 3.5rem;
            color: white;
        }

        h1 {
            color: #28a745;
            font-size: 2.5rem;
            margin-bottom: 20px;
        }

        .amount-display {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            margin: 25px 0;
        }

        .amount-label {
            color: #6c757d;
            font-size: 1.1rem;
            margin-bottom: 10px;
        }

        .amount-value {
            font-size: 2.5rem;
            font-weight: 800;
            color: #28a745;
        }

        .currency {
            color: #f9d806;
            font-weight: 600;
        }

        .booking-details {
            text-align: left;
            background: #f8f9fa;
            padding: 25px;
            border-radius: 12px;
            margin: 25px 0;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #dee2e6;
        }

        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .detail-label {
            color: #6c757d;
        }

        .detail-value {
            color: #343a40;
            font-weight: 600;
        }

        .confirmation-number {
            background: #e9ecef;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            font-family: monospace;
            font-size: 1.2rem;
            letter-spacing: 2px;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            flex: 1;
            padding: 18px;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #f9d806 0%, #ffee80 100%);
            color: #130f40;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(249, 216, 6, 0.3);
        }

        .btn-secondary {
            background: #e9ecef;
            color: #6c757d;
            border: 2px solid #dee2e6;
        }

        .btn-secondary:hover {
            background: #dee2e6;
        }

        .email-notice {
            color: #6c757d;
            font-size: 0.95rem;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }

        .timer {
            margin-top: 20px;
            color: #6c757d;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .success-container {
                padding: 30px;
            }

            h1 {
                font-size: 2rem;
            }

            .amount-value {
                font-size: 2rem;
            }

            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>
        
        <h1>Payment Successful!</h1>
        <p>Your booking has been confirmed. Thank you for choosing Travel_X!</p>
        
        <div class="amount-display">
            <div class="amount-label">Amount Paid</div>
            <div class="amount-value">
                <span class="currency">NPR</span> <?php echo number_format($amount); ?>
            </div>
        </div>
        
        <div class="booking-details">
            <div class="detail-row">
                <span class="detail-label">Booking Reference:</span>
                <span class="detail-value">TRAVELX-<?php echo strtoupper(substr(md5($booking_id), 0, 8)); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Vehicle:</span>
                <span class="detail-value"><?php echo htmlspecialchars($booking['vehicle_name']); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Pick-up Date:</span>
                <span class="detail-value"><?php echo date('M d, Y', strtotime($booking['pickup_date'])); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Return Date:</span>
                <span class="detail-value"><?php echo date('M d, Y', strtotime($booking['return_date'])); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Payment Method:</span>
                <span class="detail-value"><?php echo ucfirst(str_replace('_', ' ', $payment_method)); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Payment Date:</span>
                <span class="detail-value"><?php echo date('M d, Y H:i'); ?></span>
            </div>
        </div>
        
        <div class="confirmation-number">
            TX-<?php echo strtoupper(uniqid()); ?>
        </div>
        
        <div class="action-buttons">
            <a href="bookings.php" class="btn btn-primary">
                <i class="fas fa-calendar-check"></i>
                View My Bookings
            </a>
            <a href="homepage.php" class="btn btn-secondary">
                <i class="fas fa-home"></i>
                Back to Home
            </a>
        </div>
        
        <div class="email-notice">
            <i class="fas fa-envelope"></i>
            A confirmation email has been sent to your registered email address.
        </div>
        
        <div class="timer">
            <p>You will be redirected to your bookings page in <span id="countdown">10</span> seconds...</p>
        </div>
    </div>
    
    <script>
        // Auto redirect countdown
        let countdown = 10;
        const countdownElement = document.getElementById('countdown');
        
        const timer = setInterval(() => {
            countdown--;
            countdownElement.textContent = countdown;
            
            if (countdown <= 0) {
                clearInterval(timer);
                window.location.href = 'bookings.php';
            }
        }, 1000);
        
        // Print receipt on Ctrl+P
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
        });
        
        // Generate PDF receipt (simulated)
        function generateReceipt() {
            alert('Receipt PDF will be generated and downloaded.');
            // In production, this would call a PHP script to generate PDF
        }
        
        // Share booking
        function shareBooking() {
            if (navigator.share) {
                navigator.share({
                    title: 'Travel_X Booking Confirmation',
                    text: 'I just booked a vehicle with Travel_X!',
                    url: window.location.href
                });
            } else {
                alert('Share link copied to clipboard!');
                navigator.clipboard.writeText(window.location.href);
            }
        }
    </script>
</body>
</html>