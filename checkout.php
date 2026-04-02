<?php
session_start();
include_once(__DIR__ . "/connection.php");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: loging.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';
$vehicle = null;

// Get vehicle ID from URL
$vehicle_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($vehicle_id == 0) {
    header("Location: vehicles.php");
    exit();
}

// Fetch vehicle details from database
$sql = "SELECT * FROM vehicles WHERE id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Database error: " . $conn->error);
}

$stmt->bind_param("i", $vehicle_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: vehicles.php");
    exit();
}

$vehicle = $result->fetch_assoc();
$stmt->close();

// Fetch user details
$user_sql = "SELECT name, email, phone FROM users WHERE id = ?";
$user_stmt = $conn->prepare($user_sql);

if (!$user_stmt) {
    die("Database error: " . $conn->error);
}

$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();
$user_stmt->close();

// Drop existing bookings table if it has wrong structure and recreate
$conn->query("DROP TABLE IF EXISTS bookings");

// Create bookings table with correct structure
$create_bookings_table = "CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    vehicle_id INT NOT NULL,
    pickup_date DATE NOT NULL,
    return_date DATE NOT NULL,
    total_days INT NOT NULL,
    pickup_location VARCHAR(255),
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'active', 'completed', 'cancelled') DEFAULT 'pending',
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    cancelled_date TIMESTAMP NULL,
    payment_method VARCHAR(50) DEFAULT 'esewa',
    transaction_id VARCHAR(100)
)";

if (!$conn->query($create_bookings_table)) {
    die("Failed to create bookings table: " . $conn->error);
}

// Handle form submission for booking
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_booking'])) {
    $pickup_date = $_POST['pickup_date'] ?? '';
    $return_date = $_POST['return_date'] ?? '';
    $pickup_location = $_POST['pickup_location'] ?? '';
    $payment_method = $_POST['payment_method'] ?? 'esewa';
    
    $errors = [];
    
    // Validate dates
    if (empty($pickup_date)) {
        $errors[] = "Pickup date is required";
    }
    
    if (empty($return_date)) {
        $errors[] = "Return date is required";
    }
    
    if (!empty($pickup_date) && !empty($return_date)) {
        $pickup = new DateTime($pickup_date);
        $return = new DateTime($return_date);
        $today = new DateTime();
        
        if ($pickup < $today) {
            $errors[] = "Pickup date cannot be in the past";
        }
        
        if ($return <= $pickup) {
            $errors[] = "Return date must be after pickup date";
        }
        
        $total_days = $pickup->diff($return)->days;
        if ($total_days == 0) {
            $total_days = 1;
        }
    } else {
        $total_days = 1;
    }
    
    if (empty($errors)) {
        $total_amount = floatval($vehicle['daily_rate']) * $total_days;
        $transaction_uuid = uniqid("txn_" . $user_id . "_");
        
        // Save booking to database
        $booking_sql = "INSERT INTO bookings (user_id, vehicle_id, pickup_date, return_date, total_days, pickup_location, total_amount, status, payment_method, transaction_id) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', ?, ?)";
        $booking_stmt = $conn->prepare($booking_sql);
        
        if (!$booking_stmt) {
            $error = "Failed to prepare booking query: " . $conn->error;
        } else {
            $booking_stmt->bind_param("iissiisss", $user_id, $vehicle_id, $pickup_date, $return_date, $total_days, $pickup_location, $total_amount, $payment_method, $transaction_uuid);
            
            if ($booking_stmt->execute()) {
                $booking_id = $booking_stmt->insert_id;
                $booking_stmt->close();
                
                if ($payment_method == 'esewa') {
                    // Redirect to eSewa payment
                    $_SESSION['current_booking_id'] = $booking_id;
                    $_SESSION['booking_amount'] = $total_amount;
                    ?>
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <meta charset="UTF-8">
                        <title>Redirecting to eSewa...</title>
                        <style>
                            body { font-family: 'Poppins', sans-serif; text-align: center; padding: 50px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
                            .loader { border: 4px solid #f3f3f3; border-top: 4px solid #1f6e43; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: 20px auto; }
                            @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
                            .container { background: white; border-radius: 20px; padding: 40px; max-width: 500px; margin: 0 auto; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
                            h2 { color: #333; margin-bottom: 20px; }
                            p { color: #666; }
                        </style>
                    </head>
                    <body>
                        <div class="container">
                            <h2><i class="fas fa-credit-card"></i> Redirecting to eSewa</h2>
                            <div class="loader"></div>
                            <p>Please wait while we redirect you to complete your payment...</p>
                        </div>
                        
                        <form id="esewaForm" action="https://rc-epay.esewa.com.np/api/epay/main/v2/form" method="POST">
                            <input type="hidden" name="amount" value="<?php echo $total_amount; ?>">
                            <input type="hidden" name="tax_amount" value="0">
                            <input type="hidden" name="total_amount" value="<?php echo $total_amount; ?>">
                            <input type="hidden" name="transaction_uuid" value="<?php echo $transaction_uuid; ?>">
                            <input type="hidden" name="product_code" value="EPAYTEST">
                            <input type="hidden" name="product_service_charge" value="0">
                            <input type="hidden" name="product_delivery_charge" value="0">
                            <input type="hidden" name="success_url" value="http://<?php echo $_SERVER['HTTP_HOST']; ?>/TRAVEL_X/payment-success.php">
                            <input type="hidden" name="failure_url" value="http://<?php echo $_SERVER['HTTP_HOST']; ?>/TRAVEL_X/payment-failure.php">
                            <input type="hidden" name="signed_field_names" value="total_amount,transaction_uuid,product_code">
                            <input type="hidden" name="signature" value="<?php echo base64_encode(hash_hmac('sha256', "total_amount=$total_amount,transaction_uuid=$transaction_uuid,product_code=EPAYTEST", '8gBm/:&EnhH.1/q', true)); ?>">
                        </form>
                        
                        <script>
                            document.getElementById('esewaForm').submit();
                        </script>
                    </body>
                    </html>
                    <?php
                    exit();
                } else {
                    // Cash on delivery or other payment methods
                    $_SESSION['success'] = "Booking confirmed! Please complete payment at pickup.";
                    header("Location: bookings.php");
                    exit();
                }
            } else {
                $error = "Failed to create booking: " . $booking_stmt->error;
                $booking_stmt->close();
            }
        }
    } else {
        $error = implode("<br>", $errors);
    }
}

// Calculate daily rate
$daily_rate = floatval($vehicle['daily_rate']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - <?php echo htmlspecialchars($vehicle['brand'] . ' ' . $vehicle['model']); ?> | Travel_X</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
            padding: 80px 20px 40px;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(10px);
            padding: 1rem 2rem;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
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

        .nav-links a:hover {
            color: #1f6e43;
        }

        .checkout-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .checkout-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .checkout-header h1 {
            font-size: 2.5rem;
            color: white;
            margin-bottom: 0.5rem;
        }

        .checkout-header p {
            color: rgba(255,255,255,0.8);
        }

        .checkout-grid {
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            gap: 2rem;
        }

        .vehicle-summary {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 100px;
        }

        .vehicle-image {
            height: 250px;
            background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .vehicle-image i {
            font-size: 5rem;
            color: #1f6e43;
        }

        .vehicle-info {
            padding: 1.5rem;
        }

        .vehicle-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .vehicle-type {
            color: #1f6e43;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .vehicle-specs {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin: 1rem 0;
            padding: 1rem 0;
            border-top: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
        }

        .spec {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
            color: #666;
        }

        .spec i {
            color: #1f6e43;
        }

        .price {
            font-size: 1.8rem;
            font-weight: 700;
            color: #1f6e43;
            margin: 1rem 0;
        }

        .price span {
            font-size: 0.9rem;
            color: #666;
            font-weight: normal;
        }

        .booking-form {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .form-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #1f6e43;
        }

        .form-group {
            margin-bottom: 1.2rem;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.85rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 0.9rem;
            font-family: inherit;
            transition: all 0.3s;
            outline: none;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #1f6e43;
            box-shadow: 0 0 0 3px rgba(31, 110, 67, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .cost-breakdown {
            background: #f8fafc;
            border-radius: 12px;
            padding: 1rem;
            margin: 1.5rem 0;
        }

        .cost-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            font-size: 0.9rem;
        }

        .cost-item.total {
            border-top: 2px solid #e2e8f0;
            margin-top: 0.5rem;
            padding-top: 0.8rem;
            font-weight: 700;
            font-size: 1.1rem;
            color: #1f6e43;
        }

        .payment-methods {
            margin: 1.5rem 0;
        }

        .payment-option {
            display: flex;
            align-items: center;
            padding: 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            margin-bottom: 0.8rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .payment-option.selected {
            border-color: #1f6e43;
            background: rgba(31, 110, 67, 0.05);
        }

        .payment-option input {
            margin-right: 1rem;
            accent-color: #1f6e43;
        }

        .payment-option i {
            font-size: 1.5rem;
            margin-right: 1rem;
            color: #1f6e43;
        }

        .payment-option .payment-info {
            flex: 1;
        }

        .payment-option .payment-name {
            font-weight: 600;
            color: #333;
        }

        .payment-option .payment-desc {
            font-size: 0.75rem;
            color: #666;
        }

        .btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #1f6e43, #2b9b5e);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(31, 110, 67, 0.3);
        }

        .alert {
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @media (max-width: 768px) {
            .checkout-grid {
                grid-template-columns: 1fr;
            }
            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }
            .vehicle-summary {
                position: static;
            }
            .navbar {
                flex-direction: column;
                gap: 1rem;
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
        <a href="bookings.php"><i class="fas fa-bookmark"></i> My Bookings</a>
        <a href="profile.php"><i class="fas fa-user-circle"></i> Profile</a>
        <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
        <a href="logout.php" style="color:#e74c3c;"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="checkout-container">
    <div class="checkout-header">
        <h1><i class="fas fa-shopping-cart"></i> Complete Your Booking</h1>
        <p>Review vehicle details and confirm your rental</p>
    </div>

    <?php if ($error): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-triangle"></i>
        <div><?php echo $error; ?></div>
    </div>
    <?php endif; ?>

    <div class="checkout-grid">
        <div class="vehicle-summary">
            <div class="vehicle-image">
                <i class="fas fa-car"></i>
            </div>
            <div class="vehicle-info">
                <h2 class="vehicle-title"><?php echo htmlspecialchars($vehicle['brand'] . ' ' . $vehicle['model']); ?></h2>
                <div class="vehicle-type"><?php echo htmlspecialchars($vehicle['type']); ?></div>
                <div class="vehicle-specs">
                    <span class="spec"><i class="fas fa-calendar"></i> <?php echo $vehicle['year']; ?></span>
                    <span class="spec"><i class="fas fa-cogs"></i> <?php echo $vehicle['transmission']; ?></span>
                    <span class="spec"><i class="fas fa-gas-pump"></i> <?php echo $vehicle['fuel_type']; ?></span>
                    <span class="spec"><i class="fas fa-tachometer-alt"></i> <?php echo $vehicle['top_speed']; ?> mph</span>
                    <span class="spec"><i class="fas fa-palette"></i> <?php echo $vehicle['color']; ?></span>
                </div>
                <div class="price">
                    NPR <?php echo number_format($daily_rate, 0); ?> <span>/ day</span>
                </div>
            </div>
        </div>

        <div class="booking-form">
            <form method="POST" action="" id="bookingForm">
                <div class="form-title">
                    <i class="fas fa-calendar-check"></i> Rental Details
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-calendar-alt"></i> Pickup Date</label>
                        <input type="date" name="pickup_date" id="pickup_date" required min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-calendar-check"></i> Return Date</label>
                        <input type="date" name="return_date" id="return_date" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-map-marker-alt"></i> Pickup Location</label>
                    <select name="pickup_location" required>
                        <option value="">Select pickup location</option>
                        <option value="Kathmandu">Kathmandu - Thamel</option>
                        <option value="Kathmandu Airport">Tribhuvan International Airport</option>
                        <option value="Pokhara">Pokhara - Lakeside</option>
                        <option value="Chitwan">Chitwan - Sauraha</option>
                        <option value="Lumbini">Lumbini</option>
                        <option value="Bhaktapur">Bhaktapur - Durbar Square</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Your Information</label>
                    <input type="text" value="<?php echo htmlspecialchars($user['name']); ?>" disabled style="background:#f1f5f9;">
                    <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled style="background:#f1f5f9; margin-top:0.5rem;">
                </div>
                
                <div class="cost-breakdown" id="costBreakdown">
                    <div class="cost-item">
                        <span>Daily Rate:</span>
                        <span>NPR <?php echo number_format($daily_rate, 0); ?></span>
                    </div>
                    <div class="cost-item">
                        <span>Duration:</span>
                        <span id="durationDisplay">0 days</span>
                    </div>
                    <div class="cost-item total">
                        <span>Total Amount:</span>
                        <span id="totalAmount">NPR 0</span>
                    </div>
                </div>
                
                <div class="form-title" style="margin-top: 0;">
                    <i class="fas fa-credit-card"></i> Payment Method
                </div>
                
                <div class="payment-methods">
                    <label class="payment-option selected">
                        <input type="radio" name="payment_method" value="esewa" checked>
                        <i class="fas fa-wallet"></i>
                        <div class="payment-info">
                            <div class="payment-name">eSewa</div>
                            <div class="payment-desc">Pay using eSewa wallet or card</div>
                        </div>
                    </label>
                    
                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="cash">
                        <i class="fas fa-money-bill-wave"></i>
                        <div class="payment-info">
                            <div class="payment-name">Cash on Pickup</div>
                            <div class="payment-desc">Pay when you receive the vehicle</div>
                        </div>
                    </label>
                </div>
                
                <button type="submit" name="confirm_booking" class="btn">
                    <i class="fas fa-check-circle"></i> Confirm & Proceed
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    const dailyRate = <?php echo $daily_rate; ?>;
    const pickupDate = document.getElementById('pickup_date');
    const returnDate = document.getElementById('return_date');
    const durationDisplay = document.getElementById('durationDisplay');
    const totalAmountSpan = document.getElementById('totalAmount');
    
    function calculateTotal() {
        if (pickupDate.value && returnDate.value) {
            const pickup = new Date(pickupDate.value);
            const ret = new Date(returnDate.value);
            
            if (ret > pickup) {
                const diffTime = Math.abs(ret - pickup);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                
                if (diffDays > 0) {
                    durationDisplay.textContent = diffDays + ' day' + (diffDays > 1 ? 's' : '');
                    const total = diffDays * dailyRate;
                    totalAmountSpan.textContent = 'NPR ' + total.toLocaleString();
                } else {
                    durationDisplay.textContent = '1 day';
                    totalAmountSpan.textContent = 'NPR ' + dailyRate.toLocaleString();
                }
            } else {
                durationDisplay.textContent = 'Invalid dates';
                totalAmountSpan.textContent = 'NPR 0';
            }
        }
    }
    
    pickupDate.addEventListener('change', function() {
        const minReturn = new Date(this.value);
        minReturn.setDate(minReturn.getDate() + 1);
        returnDate.min = minReturn.toISOString().split('T')[0];
        if (returnDate.value && new Date(returnDate.value) <= new Date(this.value)) {
            returnDate.value = minReturn.toISOString().split('T')[0];
        }
        calculateTotal();
    });
    
    returnDate.addEventListener('change', calculateTotal);
    
    const paymentOptions = document.querySelectorAll('.payment-option');
    paymentOptions.forEach(option => {
        option.addEventListener('click', function() {
            paymentOptions.forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
            const radio = this.querySelector('input[type="radio"]');
            radio.checked = true;
        });
    });
</script>

</body>
</html>