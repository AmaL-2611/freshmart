<?php
require_once 'includes/init.php';

$user = new User($db);

if (!$user->isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$orderId = $_GET['order_id'] ?? 0;

// Get order details
$stmt = $db->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$orderId, $user->getId()]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: index.php');
    exit();
}

$pageTitle = 'Order Success - FreshMart';
require_once 'includes/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card text-center">
                <div class="card-body py-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 80px;"></i>
                    </div>
                    <h2 class="text-success mb-3">Order Placed Successfully!</h2>
                    <p class="lead">Thank you for your order</p>
                    <p class="text-muted">Your order ID is: <strong>#<?php echo $orderId; ?></strong></p>
                    
                    <div class="alert alert-info mt-4">
                        <i class="fas fa-info-circle"></i> 
                        You will receive a confirmation email shortly with your order details.
                    </div>

                    <div class="card mt-4">
                        <div class="card-body text-start">
                            <h5 class="card-title">Order Details</h5>
                            <hr>
                            <div class="row">
                                <div class="col-6">
                                    <p><strong>Order ID:</strong></p>
                                    <p><strong>Order Date:</strong></p>
                                    <p><strong>Total Amount:</strong></p>
                                    <p><strong>Payment Method:</strong></p>
                                    <p><strong>Status:</strong></p>
                                </div>
                                <div class="col-6">
                                    <p>#<?php echo $order['id']; ?></p>
                                    <p><?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?></p>
                                    <p class="text-success">₹<?php echo number_format($order['total_amount'], 2); ?></p>
                                    <p><?php echo strtoupper($order['payment_method']); ?></p>
                                    <p><span class="badge bg-warning"><?php echo ucfirst($order['status']); ?></span></p>
                                </div>
                            </div>
                            <hr>
                            <p><strong>Shipping Address:</strong></p>
                            <p><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="index.php" class="btn btn-success me-2">
                            <i class="fas fa-home"></i> Continue Shopping
                        </a>
                        <a href="my-orders.php" class="btn btn-outline-primary">
                            <i class="fas fa-list"></i> View My Orders
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
