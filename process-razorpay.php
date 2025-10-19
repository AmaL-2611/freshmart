<?php
require_once 'includes/init.php';

header('Content-Type: application/json');

// Initialize User and Cart classes
$user = new User($db);
$cart = new Cart($db);

// Check if user is logged in
if (!$user->isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

// Check if cart is empty
$cartItems = $cart->getItems();
if (empty($cartItems)) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty']);
    exit();
}

// Get form data
$shippingAddress = trim($_POST['shipping_address'] ?? '');
$paymentMethod = $_POST['payment_method'] ?? '';
$razorpayPaymentId = $_POST['razorpay_payment_id'] ?? '';
$razorpayOrderId = $_POST['razorpay_order_id'] ?? '';
$razorpaySignature = $_POST['razorpay_signature'] ?? '';

// Validate data
if (empty($shippingAddress)) {
    echo json_encode(['success' => false, 'message' => 'Shipping address is required']);
    exit();
}

if (empty($razorpayPaymentId)) {
    echo json_encode(['success' => false, 'message' => 'Payment ID is missing']);
    exit();
}

// Optional: Verify Razorpay signature for security
// Uncomment this when you have actual Razorpay credentials
/*
$razorpayKeySecret = 'YOUR_KEY_SECRET';
$generatedSignature = hash_hmac('sha256', $razorpayOrderId . '|' . $razorpayPaymentId, $razorpayKeySecret);

if ($generatedSignature !== $razorpaySignature) {
    echo json_encode(['success' => false, 'message' => 'Payment verification failed']);
    exit();
}
*/

// Process the order
$result = $cart->processOrder($user->getId(), $shippingAddress, 'razorpay');

if ($result['success']) {
    // Store Razorpay payment details in database
    try {
        $stmt = $db->prepare("
            INSERT INTO payment_transactions 
            (order_id, payment_id, payment_method, amount, status) 
            VALUES (?, ?, 'razorpay', ?, 'success')
        ");
        
        $cartTotal = 0;
        foreach ($cartItems as $item) {
            $cartTotal += $item['price'] * $item['quantity'];
        }
        $grandTotal = $cartTotal + 50; // Including delivery fee
        
        $stmt->execute([
            $result['order_id'],
            $razorpayPaymentId,
            $grandTotal
        ]);
    } catch (Exception $e) {
        // Log error but don't fail the order
        error_log('Failed to store payment transaction: ' . $e->getMessage());
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Order placed successfully',
        'order_id' => $result['order_id']
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => $result['message']
    ]);
}
?>
