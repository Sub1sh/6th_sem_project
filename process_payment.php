<?php
require_once 'config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Get input data
$data = json_decode(file_get_contents('php://input'), true);
$booking_id = $data['booking_id'] ?? null;
$payment_method = $data['payment_method'] ?? null;
$amount = $data['amount'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

if (!$booking_id || !$payment_method || !$amount || !$user_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request data']);
    exit();
}

try {
    $pdo->beginTransaction();
    
    // Verify booking exists and belongs to user
    $stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ? AND user_id = ? AND payment_status = 'pending'");
    $stmt->execute([$booking_id, $user_id]);
    $booking = $stmt->fetch();
    
    if (!$booking) {
        throw new Exception('Booking not found or already paid');
    }
    
    // Process payment based on method
    switch ($payment_method) {
        case 'esewa':
            $result = processEsewaPayment($amount, $booking_id, $data);
            break;
        case 'khalti':
            $result = processKhaltiPayment($amount, $booking_id, $data);
            break;
        case 'credit_card':
            $result = processCardPayment($amount, $booking_id, $data);
            break;
        case 'connect_ips':
            $result = processBankPayment($amount, $booking_id, $data);
            break;
        case 'cash':
            $result = ['success' => true, 'transaction_id' => 'CASH-' . time()];
            break;
        default:
            throw new Exception('Invalid payment method');
    }
    
    if (!$result['success']) {
        throw new Exception($result['message'] ?? 'Payment failed');
    }
    
    // Update booking payment status
    $stmt = $pdo->prepare("UPDATE bookings SET 
        payment_status = 'paid',
        payment_method = ?,
        payment_date = NOW(),
        total_amount = ?,
        transaction_id = ?
        WHERE id = ?");
    
    $stmt->execute([
        $payment_method,
        $amount,
        $result['transaction_id'] ?? null,
        $booking_id
    ]);
    
    // Create payment record
    $stmt = $pdo->prepare("INSERT INTO payments 
        (booking_id, user_id, amount, currency, payment_method, transaction_id, status) 
        VALUES (?, ?, ?, 'NPR', ?, ?, 'completed')");
    
    $stmt->execute([
        $booking_id,
        $user_id,
        $amount,
        $payment_method,
        $result['transaction_id'] ?? null
    ]);
    
    $payment_id = $pdo->lastInsertId();
    
    $pdo->commit();
    
    // Send confirmation email
    sendConfirmationEmail($user_id, $booking_id, $payment_id);
    
    echo json_encode([
        'success' => true,
        'payment_id' => $payment_id,
        'transaction_id' => $result['transaction_id'] ?? null,
        'message' => 'Payment successful'
    ]);
    
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// Payment gateway processing functions
function processEsewaPayment($amount, $booking_id, $data) {
    // eSewa API integration
    // In production, you would call eSewa's API
    $transaction_id = 'ESEWA-' . time() . '-' . rand(1000, 9999);
    
    // Simulate API call
    // $response = callEsewaAPI($amount, $booking_id, $data);
    
    return [
        'success' => true,
        'transaction_id' => $transaction_id,
        'message' => 'eSewa payment successful'
    ];
}

function processKhaltiPayment($amount, $booking_id, $data) {
    // Khalti API integration
    $transaction_id = 'KHALTI-' . time() . '-' . rand(1000, 9999);
    
    // Simulate API call
    // $response = callKhaltiAPI($amount, $booking_id, $data);
    
    return [
        'success' => true,
        'transaction_id' => $transaction_id,
        'message' => 'Khalti payment successful'
    ];
}

function processCardPayment($amount, $booking_id, $data) {
    // Card payment processing (would integrate with bank/payment gateway)
    // Validate card details
    $card_number = $data['card_number'] ?? '';
    $expiry = $data['expiry_date'] ?? '';
    $cvv = $data['cvv'] ?? '';
    
    if (!validateCard($card_number, $expiry, $cvv)) {
        return ['success' => false, 'message' => 'Invalid card details'];
    }
    
    $transaction_id = 'CARD-' . time() . '-' . rand(1000, 9999);
    
    return [
        'success' => true,
        'transaction_id' => $transaction_id,
        'message' => 'Card payment successful'
    ];
}

function processBankPayment($amount, $booking_id, $data) {
    // Connect IPS or bank transfer
    $transaction_id = 'BANK-' . time() . '-' . rand(1000, 9999);
    
    return [
        'success' => true,
        'transaction_id' => $transaction_id,
        'message' => 'Bank transfer initiated'
    ];
}

function validateCard($number, $expiry, $cvv) {
    // Basic validation
    if (strlen($number) < 13 || strlen($number) > 19) return false;
    if (!preg_match('/^(0[1-9]|1[0-2])\/([0-9]{2})$/', $expiry)) return false;
    if (strlen($cvv) < 3 || strlen($cvv) > 4) return false;
    
    return true;
}

function sendConfirmationEmail($user_id, $booking_id, $payment_id) {
    // Get user email and booking details
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT u.email, u.full_name, b.*, v.name as vehicle_name 
                          FROM users u 
                          JOIN bookings b ON u.id = b.user_id 
                          JOIN vehicles v ON b.vehicle_id = v.id 
                          WHERE u.id = ? AND b.id = ?");
    $stmt->execute([$user_id, $booking_id]);
    $data = $stmt->fetch();
    
    if (!$data) return;
    
    // Send email (simulated)
    $to = $data['email'];
    $subject = "Travel_X - Booking Confirmation #" . $booking_id;
    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; }
            .container { max-width: 600px; margin: 0 auto; }
            .header { background: #f9d806; padding: 20px; text-align: center; }
            .content { padding: 30px; }
            .footer { background: #f5f5f5; padding: 20px; text-align: center; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Travel_X Booking Confirmation</h2>
            </div>
            <div class='content'>
                <h3>Hello {$data['full_name']},</h3>
                <p>Your booking has been confirmed. Here are the details:</p>
                
                <h4>Booking Details:</h4>
                <p><strong>Booking ID:</strong> {$booking_id}</p>
                <p><strong>Vehicle:</strong> {$data['vehicle_name']}</p>
                <p><strong>Pick-up Date:</strong> {$data['pickup_date']}</p>
                <p><strong>Return Date:</strong> {$data['return_date']}</p>
                <p><strong>Total Amount:</strong> NPR {$data['total_amount']}</p>
                <p><strong>Payment ID:</strong> {$payment_id}</p>
                
                <p>Thank you for choosing Travel_X!</p>
            </div>
            <div class='footer'>
                <p>© 2025 Travel_X. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: Travel_X <noreply@travelx.com>" . "\r\n";
    
    // In production, use mail() or a proper email library
    // mail($to, $subject, $message, $headers);
}
?>