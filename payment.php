<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=payment');
    exit();
}

// Get booking details from session or database
if (isset($_SESSION['booking_data'])) {
    $booking = $_SESSION['booking_data'];
} else {
    // Try to get from database if session expired
    if (isset($_GET['booking_id'])) {
        $stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ? AND user_id = ?");
        $stmt->execute([$_GET['booking_id'], $_SESSION['user_id']]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    if (!$booking) {
        header('Location: vehicles.php');
        exit();
    }
}

// Calculate total amount
$total_amount = calculateTotalAmount($booking);
$service_charge = $total_amount * 0.13; // 13% service charge
$vat = $total_amount * 0.13; // 13% VAT
$grand_total = $total_amount + $service_charge + $vat;

function calculateTotalAmount($booking) {
    $base_price = $booking['daily_rate'] * $booking['days'];
    // Add extra charges if any
    $extra_charges = $booking['extra_charges'] ?? 0;
    return $base_price + $extra_charges;
}

// Payment methods
$payment_methods = [
    'esewa' => [
        'name' => 'eSewa',
        'icon' => 'fas fa-mobile-alt',
        'description' => 'Pay with eSewa wallet or QR'
    ],
    'khalti' => [
        'name' => 'Khalti',
        'icon' => 'fas fa-wallet',
        'description' => 'Pay with Khalti wallet'
    ],
    'connect_ips' => [
        'name' => 'Connect IPS',
        'icon' => 'fas fa-university',
        'description' => 'Internet banking payment'
    ],
    'credit_card' => [
        'name' => 'Credit/Debit Card',
        'icon' => 'fas fa-credit-card',
        'description' => 'Visa, MasterCard, UnionPay'
    ],
    'cash' => [
        'name' => 'Cash Payment',
        'icon' => 'fas fa-money-bill-wave',
        'description' => 'Pay at our office'
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Travel_X</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/payment.css">
    <style>
        :root {
            --primary: #f9d806;
            --primary-light: #ffee80;
            --secondary: #130f40;
            --success: #28a745;
            --danger: #dc3545;
            --light: #f8f9fa;
            --dark: #343a40;
        }

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

        .payment-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 1200px;
            overflow: hidden;
        }

        .payment-header {
            background: linear-gradient(135deg, var(--secondary) 0%, #2d3748 100%);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }

        .payment-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(to right, var(--primary), var(--primary-light));
        }

        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .logo-icon {
            font-size: 2.5rem;
            color: var(--primary);
        }

        .logo-text {
            font-size: 2rem;
            font-weight: 800;
        }

        .logo-text span:first-child {
            color: white;
        }

        .logo-text span:last-child {
            color: var(--primary);
        }

        .payment-header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .payment-header p {
            color: #a0aec0;
            font-size: 1.1rem;
        }

        .payment-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
        }

        .payment-left {
            padding: 40px;
            background: var(--light);
        }

        .payment-right {
            padding: 40px;
            background: white;
        }

        .booking-summary {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .booking-summary h3 {
            color: var(--secondary);
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--primary-light);
            font-size: 1.5rem;
        }

        .vehicle-info {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
        }

        .vehicle-image {
            width: 100px;
            height: 70px;
            border-radius: 10px;
            overflow: hidden;
        }

        .vehicle-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .vehicle-details h4 {
            color: var(--secondary);
            font-size: 1.3rem;
            margin-bottom: 5px;
        }

        .vehicle-details p {
            color: #718096;
            font-size: 0.95rem;
        }

        .booking-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
        }

        .detail-label {
            color: #718096;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }

        .detail-value {
            color: var(--secondary);
            font-weight: 600;
            font-size: 1.1rem;
        }

        .amount-breakdown {
            margin-top: 25px;
            border-top: 2px solid #e2e8f0;
            padding-top: 20px;
        }

        .amount-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .amount-label {
            color: #718096;
        }

        .amount-value {
            color: var(--secondary);
            font-weight: 500;
        }

        .total-row {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #e2e8f0;
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--secondary);
        }

        .nrs {
            color: var(--primary);
        }

        /* Payment Methods */
        .payment-methods {
            margin-bottom: 30px;
        }

        .payment-methods h3 {
            color: var(--secondary);
            margin-bottom: 20px;
            font-size: 1.5rem;
        }

        .method-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .method-card {
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .method-card:hover {
            border-color: var(--primary);
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(249, 216, 6, 0.1);
        }

        .method-card.active {
            border-color: var(--primary);
            background: linear-gradient(135deg, rgba(249, 216, 6, 0.1) 0%, rgba(255, 238, 128, 0.1) 100%);
        }

        .method-icon {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 15px;
        }

        .method-name {
            color: var(--secondary);
            font-weight: 600;
            font-size: 1.2rem;
            margin-bottom: 8px;
        }

        .method-desc {
            color: #718096;
            font-size: 0.9rem;
        }

        /* Payment Form */
        .payment-form {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .payment-form h3 {
            color: var(--secondary);
            margin-bottom: 25px;
            font-size: 1.5rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            color: var(--secondary);
            margin-bottom: 8px;
            font-weight: 500;
        }

        .form-input {
            width: 100%;
            padding: 15px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: var(--light);
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 3px rgba(249, 216, 6, 0.1);
        }

        .input-with-icon {
            position: relative;
        }

        .input-with-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
        }

        .input-with-icon input {
            padding-left: 45px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        /* eSewa/Khalti Integration */
        .digital-wallet {
            text-align: center;
            padding: 30px;
        }

        .qr-code {
            width: 200px;
            height: 200px;
            margin: 20px auto;
            padding: 10px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .qr-code img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        /* Action Buttons */
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
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: var(--secondary);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(249, 216, 6, 0.3);
        }

        .btn-secondary {
            background: var(--light);
            color: var(--secondary);
            border: 2px solid #e2e8f0;
        }

        .btn-secondary:hover {
            background: #e2e8f0;
        }

        /* Security Badge */
        .security-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .security-badge i {
            color: var(--success);
            font-size: 1.5rem;
        }

        .security-badge span {
            color: #718096;
            font-size: 0.9rem;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .payment-content {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .payment-header h1 {
                font-size: 2rem;
            }

            .payment-left, .payment-right {
                padding: 25px;
            }

            .booking-details {
                grid-template-columns: 1fr;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .method-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 576px) {
            .method-grid {
                grid-template-columns: 1fr;
            }

            .action-buttons {
                flex-direction: column;
            }

            .vehicle-info {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <div class="payment-header">
            <div class="logo">
                <i class="fas fa-car logo-icon"></i>
                <div class="logo-text">
                    <span>Travel</span><span>X</span>
                </div>
            </div>
            <h1>Complete Your Payment</h1>
            <p>Secure payment gateway with Nepali currency support</p>
        </div>

        <div class="payment-content">
            <!-- Left Column: Booking Summary -->
            <div class="payment-left">
                <div class="booking-summary">
                    <h3>Booking Summary</h3>
                    
                    <div class="vehicle-info">
                        <div class="vehicle-image">
                            <img src="images/<?php echo htmlspecialchars($booking['vehicle_image'] ?? 'default.jpg'); ?>" alt="Vehicle">
                        </div>
                        <div class="vehicle-details">
                            <h4><?php echo htmlspecialchars($booking['vehicle_name']); ?></h4>
                            <p><?php echo htmlspecialchars($booking['vehicle_type']); ?> • <?php echo htmlspecialchars($booking['transmission']); ?></p>
                        </div>
                    </div>

                    <div class="booking-details">
                        <div class="detail-item">
                            <span class="detail-label">Pick-up Date</span>
                            <span class="detail-value"><?php echo date('M d, Y', strtotime($booking['pickup_date'])); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Return Date</span>
                            <span class="detail-value"><?php echo date('M d, Y', strtotime($booking['return_date'])); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Rental Days</span>
                            <span class="detail-value"><?php echo $booking['days']; ?> days</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Daily Rate</span>
                            <span class="detail-value"><span class="nrs">NPR</span> <?php echo number_format($booking['daily_rate']); ?></span>
                        </div>
                    </div>

                    <div class="amount-breakdown">
                        <div class="amount-row">
                            <span class="amount-label">Subtotal</span>
                            <span class="amount-value"><span class="nrs">NPR</span> <?php echo number_format($total_amount); ?></span>
                        </div>
                        <div class="amount-row">
                            <span class="amount-label">Service Charge (13%)</span>
                            <span class="amount-value"><span class="nrs">NPR</span> <?php echo number_format($service_charge); ?></span>
                        </div>
                        <div class="amount-row">
                            <span class="amount-label">VAT (13%)</span>
                            <span class="amount-value"><span class="nrs">NPR</span> <?php echo number_format($vat); ?></span>
                        </div>
                        <div class="amount-row total-row">
                            <span class="amount-label">Total Amount</span>
                            <span class="amount-value"><span class="nrs">NPR</span> <?php echo number_format($grand_total); ?></span>
                        </div>
                    </div>
                </div>

                <div class="payment-methods">
                    <h3>Select Payment Method</h3>
                    <div class="method-grid" id="paymentMethods">
                        <?php foreach ($payment_methods as $key => $method): ?>
                            <div class="method-card" data-method="<?php echo $key; ?>">
                                <i class="<?php echo $method['icon']; ?> method-icon"></i>
                                <div class="method-name"><?php echo $method['name']; ?></div>
                                <div class="method-desc"><?php echo $method['description']; ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Right Column: Payment Form -->
            <div class="payment-right">
                <div id="paymentFormContainer">
                    <!-- Credit Card Form (Default) -->
                    <form id="creditCardForm" class="payment-form" style="display: block;">
                        <h3>Credit/Debit Card Details</h3>
                        
                        <div class="form-group">
                            <label class="form-label">Card Number</label>
                            <div class="input-with-icon">
                                <i class="fas fa-credit-card"></i>
                                <input type="text" class="form-input" placeholder="1234 5678 9012 3456" maxlength="19" id="cardNumber">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Expiry Date</label>
                                <input type="text" class="form-input" placeholder="MM/YY" id="expiryDate">
                            </div>
                            <div class="form-group">
                                <label class="form-label">CVV</label>
                                <div class="input-with-icon">
                                    <i class="fas fa-lock"></i>
                                    <input type="password" class="form-input" placeholder="123" maxlength="4" id="cvv">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Card Holder Name</label>
                            <input type="text" class="form-input" placeholder="John Doe" id="cardHolder">
                        </div>

                        <div class="action-buttons">
                            <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                                <i class="fas fa-arrow-left"></i>
                                Back
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-lock"></i>
                                Pay NPR <?php echo number_format($grand_total); ?>
                            </button>
                        </div>
                    </form>

                    <!-- eSewa Form -->
                    <div id="esewaForm" class="payment-form digital-wallet" style="display: none;">
                        <h3>Pay with eSewa</h3>
                        <p>Scan the QR code or click to pay</p>
                        
                        <div class="qr-code">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?php echo urlencode('esewa://pay?amount=' . $grand_total . '&pid=' . $booking['id']); ?>" alt="eSewa QR Code">
                        </div>

                        <div class="action-buttons">
                            <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                                <i class="fas fa-arrow-left"></i>
                                Back
                            </button>
                            <button type="button" class="btn btn-primary" onclick="initiateEsewa()">
                                <i class="fas fa-mobile-alt"></i>
                                Pay with eSewa
                            </button>
                        </div>
                    </div>

                    <!-- Khalti Form -->
                    <div id="khaltiForm" class="payment-form digital-wallet" style="display: none;">
                        <h3>Pay with Khalti</h3>
                        <p>Click below to proceed with Khalti payment</p>
                        
                        <div class="qr-code">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?php echo urlencode('khalti://payment?amount=' . $grand_total . '&pid=' . $booking['id']); ?>" alt="Khalti QR Code">
                        </div>

                        <div class="action-buttons">
                            <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                                <i class="fas fa-arrow-left"></i>
                                Back
                            </button>
                            <button type="button" class="btn btn-primary" onclick="initiateKhalti()">
                                <i class="fas fa-wallet"></i>
                                Pay with Khalti
                            </button>
                        </div>
                    </div>

                    <!-- Connect IPS Form -->
                    <div id="connectIpsForm" class="payment-form" style="display: none;">
                        <h3>Connect IPS Payment</h3>
                        
                        <div class="form-group">
                            <label class="form-label">Select Bank</label>
                            <select class="form-input" id="bankSelect">
                                <option value="">Select your bank</option>
                                <option value="nabil">Nabil Bank</option>
                                <option value="nrb">NRB Commercial Bank</option>
                                <option value="global">Global IME Bank</option>
                                <option value="standard">Standard Chartered Bank</option>
                                <option value="nicasia">NIC Asia Bank</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Account Number</label>
                            <input type="text" class="form-input" placeholder="Your bank account number" id="accountNumber">
                        </div>

                        <div class="action-buttons">
                            <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                                <i class="fas fa-arrow-left"></i>
                                Back
                            </button>
                            <button type="button" class="btn btn-primary" onclick="initiateConnectIPS()">
                                <i class="fas fa-university"></i>
                                Proceed to Bank
                            </button>
                        </div>
                    </div>

                    <!-- Cash Payment Form -->
                    <div id="cashForm" class="payment-form" style="display: none;">
                        <h3>Cash Payment</h3>
                        <p>Please visit our nearest office to complete your payment.</p>
                        
                        <div class="form-group">
                            <label class="form-label">Payment Deadline</label>
                            <input type="text" class="form-input" value="<?php echo date('M d, Y', strtotime('+2 days')); ?>" readonly>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Reference Number</label>
                            <input type="text" class="form-input" value="TRAVELX-<?php echo strtoupper(uniqid()); ?>" readonly>
                        </div>

                        <div class="action-buttons">
                            <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                                <i class="fas fa-arrow-left"></i>
                                Back
                            </button>
                            <button type="button" class="btn btn-primary" onclick="confirmCashPayment()">
                                <i class="fas fa-check"></i>
                                Confirm Cash Payment
                            </button>
                        </div>
                    </div>
                </div>

                <div class="security-badge">
                    <i class="fas fa-shield-alt"></i>
                    <span>Secure SSL Encryption • Your payment is 100% secure</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Payment method selection
        const methodCards = document.querySelectorAll('.method-card');
        const forms = {
            credit_card: document.getElementById('creditCardForm'),
            esewa: document.getElementById('esewaForm'),
            khalti: document.getElementById('khaltiForm'),
            connect_ips: document.getElementById('connectIpsForm'),
            cash: document.getElementById('cashForm')
        };

        let selectedMethod = 'credit_card';

        methodCards.forEach(card => {
            card.addEventListener('click', function() {
                const method = this.dataset.method;
                
                // Update active state
                methodCards.forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                
                // Show selected form
                Object.values(forms).forEach(form => form.style.display = 'none');
                if (forms[method]) {
                    forms[method].style.display = 'block';
                }
                
                selectedMethod = method;
            });
        });

        // Format card number
        document.getElementById('cardNumber').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
            let formatted = '';
            
            for (let i = 0; i < value.length; i++) {
                if (i > 0 && i % 4 === 0) {
                    formatted += ' ';
                }
                formatted += value[i];
            }
            
            e.target.value = formatted.substring(0, 19);
        });

        // Format expiry date
        document.getElementById('expiryDate').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
            
            if (value.length >= 2) {
                e.target.value = value.substring(0, 2) + '/' + value.substring(2, 4);
            } else {
                e.target.value = value;
            }
        });

        // Form submission
        document.getElementById('creditCardForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const cardNumber = document.getElementById('cardNumber').value.replace(/\s+/g, '');
            const expiryDate = document.getElementById('expiryDate').value;
            const cvv = document.getElementById('cvv').value;
            const cardHolder = document.getElementById('cardHolder').value;
            
            // Basic validation
            if (!validateCardNumber(cardNumber)) {
                alert('Please enter a valid card number');
                return;
            }
            
            if (!validateExpiryDate(expiryDate)) {
                alert('Please enter a valid expiry date (MM/YY)');
                return;
            }
            
            if (!cvv || cvv.length < 3) {
                alert('Please enter a valid CVV');
                return;
            }
            
            if (!cardHolder) {
                alert('Please enter card holder name');
                return;
            }
            
            // Process payment
            processPayment(selectedMethod, {
                card_number: cardNumber,
                expiry_date: expiryDate,
                cvv: cvv,
                card_holder: cardHolder
            });
        });

        // Payment methods
        function initiateEsewa() {
            // In production, this would redirect to eSewa's payment gateway
            const amount = <?php echo $grand_total; ?>;
            const bookingId = '<?php echo $booking['id']; ?>';
            
            // Simulate eSewa payment
            processPayment('esewa', { amount: amount, booking_id: bookingId });
        }

        function initiateKhalti() {
            // In production, this would use Khalti's API
            const amount = <?php echo $grand_total; ?>;
            const bookingId = '<?php echo $booking['id']; ?>';
            
            // Simulate Khalti payment
            processPayment('khalti', { amount: amount, booking_id: bookingId });
        }

        function initiateConnectIPS() {
            const bank = document.getElementById('bankSelect').value;
            const account = document.getElementById('accountNumber').value;
            
            if (!bank || !account) {
                alert('Please select bank and enter account number');
                return;
            }
            
            // Simulate Connect IPS payment
            processPayment('connect_ips', { bank: bank, account: account });
        }

        function confirmCashPayment() {
            if (confirm('Are you sure you want to confirm cash payment? You need to visit our office within 48 hours to complete the payment.')) {
                processPayment('cash', {});
            }
        }

        function processPayment(method, data) {
            // Show loading state
            const buttons = document.querySelectorAll('.btn-primary');
            buttons.forEach(btn => {
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                btn.disabled = true;
                
                // Restore button after 2 seconds (in real app, this would be after API response)
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                    
                    // Simulate successful payment
                    if (Math.random() > 0.1) { // 90% success rate for demo
                        window.location.href = 'payment_success.php?booking_id=<?php echo $booking["id"]; ?>&amount=<?php echo $grand_total; ?>';
                    } else {
                        alert('Payment failed. Please try again or use a different payment method.');
                    }
                }, 2000);
            });
        }

        // Validation functions
        function validateCardNumber(number) {
            // Luhn algorithm for card validation
            let sum = 0;
            let shouldDouble = false;
            
            for (let i = number.length - 1; i >= 0; i--) {
                let digit = parseInt(number.charAt(i));
                
                if (shouldDouble) {
                    if ((digit *= 2) > 9) digit -= 9;
                }
                
                sum += digit;
                shouldDouble = !shouldDouble;
            }
            
            return (sum % 10) === 0;
        }

        function validateExpiryDate(date) {
            const regex = /^(0[1-9]|1[0-2])\/([0-9]{2})$/;
            if (!regex.test(date)) return false;
            
            const [month, year] = date.split('/');
            const now = new Date();
            const currentYear = now.getFullYear() % 100;
            const currentMonth = now.getMonth() + 1;
            
            if (parseInt(year) < currentYear) return false;
            if (parseInt(year) === currentYear && parseInt(month) < currentMonth) return false;
            
            return true;
        }
    </script>
</body>
</html>