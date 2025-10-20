<?php
require_once 'includes/init.php';

// Initialize User and Cart classes
$user = new User($db);
$cart = new Cart($db);

// Check if user is logged in
if (!$user->isLoggedIn()) {
    header('Location: login.php?redirect=checkout.php');
    exit();
}

// Check if cart is empty
$cartItems = $cart->getItems();
if (empty($cartItems)) {
    header('Location: cart.php');
    exit();
}

$cartTotal = $cart->getTotal();
$deliveryFee = 50;
$grandTotal = $cartTotal + $deliveryFee;

$error = '';
$success = '';

// Process checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shippingAddress = trim($_POST['shipping_address'] ?? '');
    $paymentMethod = $_POST['payment_method'] ?? '';
    
    if (empty($shippingAddress)) {
        $error = 'Please enter shipping address';
    } elseif (empty($paymentMethod)) {
        $error = 'Please select payment method';
    } else {
        $result = $cart->processOrder($user->getId(), $shippingAddress, $paymentMethod);
        
        if ($result['success']) {
            header('Location: order-success.php?order_id=' . $result['order_id']);
            exit();
        } else {
            $error = $result['message'];
        }
    }
}

// Load Razorpay configuration
require_once 'config/razorpay.php';

$pageTitle = 'Checkout - FreshMart';
require_once 'includes/header.php';
?>

<div class="container my-5">
    <h2 class="mb-4">Checkout</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Shipping Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" value="<?php echo htmlspecialchars($user->getName()); ?>" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($user->getEmail()); ?>" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="shipping_address" class="form-label">Shipping Address <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="shipping_address" name="shipping_address" rows="3" required><?php echo htmlspecialchars($_POST['shipping_address'] ?? ''); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked>
                                <label class="form-check-label" for="cod">
                                    <i class="fas fa-money-bill-wave"></i> Cash on Delivery
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="razorpay" value="razorpay">
                                <label class="form-check-label" for="razorpay">
                                    <i class="fas fa-credit-card"></i> Pay Online (Razorpay - UPI/Card/Wallet)
                                </label>
                            </div>
                        </div>

                        <button type="button" id="placeOrderBtn" class="btn btn-success btn-lg w-100">
                            <i class="fas fa-check"></i> Place Order
                        </button>
                        
                        <!-- Hidden form for COD -->
                        <form id="codForm" method="POST" style="display: none;">
                            <input type="hidden" name="shipping_address" id="cod_address">
                            <input type="hidden" name="payment_method" value="cod">
                        </form>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <h6 class="mb-3">Items (<?php echo count($cartItems); ?>)</h6>
                    <?php foreach ($cartItems as $item): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span><?php echo htmlspecialchars($item['name']); ?> x <?php echo $item['quantity']; ?></span>
                            <span>₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                        </div>
                    <?php endforeach; ?>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span>₹<?php echo number_format($cartTotal, 2); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Delivery Fee:</span>
                        <span>₹<?php echo number_format($deliveryFee, 2); ?></span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total:</strong>
                        <strong class="text-success">₹<?php echo number_format($grandTotal, 2); ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Razorpay Script -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const placeOrderBtn = document.getElementById('placeOrderBtn');
    const shippingAddress = document.getElementById('shipping_address');
    
    placeOrderBtn.addEventListener('click', function() {
        // Validate shipping address
        if (!shippingAddress.value.trim()) {
            alert('Please enter shipping address');
            shippingAddress.focus();
            return;
        }
        
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
        
        if (paymentMethod === 'cod') {
            // Process COD order
            document.getElementById('cod_address').value = shippingAddress.value;
            document.getElementById('codForm').submit();
        } else if (paymentMethod === 'razorpay') {
            // Process Razorpay payment
            initiateRazorpayPayment();
        }
    });
    
    function initiateRazorpayPayment() {
        const amount = <?php echo $grandTotal * 100; ?>; // Amount in paise
        const options = {
            "key": "<?php echo RAZORPAY_KEY_ID; ?>",
            "amount": amount,
            "currency": "INR",
            "name": "FreshMart",
            "description": "Order Payment",
            "image": "./assets/images/logo.png",
            "handler": function (response) {
                // Payment successful
                processRazorpayOrder(response);
            },
            "prefill": {
                "name": "<?php echo htmlspecialchars($user->getName()); ?>",
                "email": "<?php echo htmlspecialchars($user->getEmail()); ?>",
                "contact": ""
            },
            "theme": {
                "color": "#28a745"
            },
            "modal": {
                "ondismiss": function() {
                    alert('Payment cancelled');
                }
            }
        };
        
        const rzp = new Razorpay(options);
        rzp.open();
    }
    
    function processRazorpayOrder(paymentResponse) {
        // Send payment details to server
        const formData = new FormData();
        formData.append('shipping_address', shippingAddress.value);
        formData.append('payment_method', 'razorpay');
        formData.append('razorpay_payment_id', paymentResponse.razorpay_payment_id);
        formData.append('razorpay_order_id', paymentResponse.razorpay_order_id || '');
        formData.append('razorpay_signature', paymentResponse.razorpay_signature || '');
        
        fetch('process-razorpay.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = 'order-success.php?order_id=' + data.order_id;
            } else {
                alert('Order processing failed: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while processing your order');
        });
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
