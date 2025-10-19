<?php
require_once '../includes/init.php';

// Initialize User class
$user = new User($db);

// Check if user is logged in and is admin
if (!$user->isLoggedIn() || !$user->isAdmin()) {
    header('Location: ../login.php');
    exit();
}

// Initialize Order class
$order = new Order($db);

// Get order ID
$orderId = $_GET['id'] ?? 0;

// Get order details
$orderDetails = $order->getOrderById($orderId);

if (!$orderDetails) {
    header('Location: orders.php');
    exit();
}

// Get order items
$orderItems = $order->getOrderItems($orderId);

$success = '';
$error = '';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $newStatus = $_POST['status'] ?? '';
    $result = $order->updateOrderStatus($orderId, $newStatus);
    
    if ($result['success']) {
        $success = $result['message'];
        // Refresh order details
        $orderDetails = $order->getOrderById($orderId);
    } else {
        $error = $result['message'];
    }
}

$pageTitle = 'Order Details - Admin Dashboard';
require_once '../includes/header.php';
?>

<div class="container-fluid mt-4">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 d-md-block bg-light sidebar">
            <div class="sidebar-sticky pt-3">
                <h6 class="sidebar-heading px-3 mt-4 mb-1 text-muted">
                    <span>Admin Panel</span>
                </h6>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="admin.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="add-product.php">
                            <i class="fas fa-plus"></i> Add Product
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="orders.php">
                            <i class="fas fa-shopping-cart"></i> Orders
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">
                            <i class="fas fa-home"></i> View Site
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="../logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main content -->
        <main role="main" class="col-md-10 ml-sm-auto px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Order #<?php echo $orderId; ?></h1>
                <a href="orders.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Orders
                </a>
            </div>

            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?php echo htmlspecialchars($success); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <!-- Order Information -->
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Order Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p><strong>Order ID:</strong> #<?php echo $orderDetails['id']; ?></p>
                                    <p><strong>Order Date:</strong> <?php echo date('d M Y, h:i A', strtotime($orderDetails['created_at'])); ?></p>
                                    <p><strong>Payment Method:</strong> 
                                        <?php if ($orderDetails['payment_method'] === 'cod'): ?>
                                            <span class="badge bg-info">Cash on Delivery</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Razorpay</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Customer Name:</strong> <?php echo htmlspecialchars($orderDetails['customer_name']); ?></p>
                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($orderDetails['customer_email']); ?></p>
                                    <p><strong>Total Amount:</strong> <span class="text-success fs-5">₹<?php echo number_format($orderDetails['total_amount'], 2); ?></span></p>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <h6>Shipping Address:</h6>
                            <p><?php echo nl2br(htmlspecialchars($orderDetails['shipping_address'])); ?></p>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">Order Items</h5>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orderItems as $item): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php if ($item['product_image']): ?>
                                                        <img src="../assets/images/products/<?php echo htmlspecialchars($item['product_image']); ?>" 
                                                             alt="<?php echo htmlspecialchars($item['product_name']); ?>" 
                                                             style="width: 50px; height: 50px; object-fit: cover;" 
                                                             class="me-3">
                                                    <?php endif; ?>
                                                    <span><?php echo htmlspecialchars($item['product_name']); ?></span>
                                                </div>
                                            </td>
                                            <td>₹<?php echo number_format($item['price'], 2); ?></td>
                                            <td><?php echo $item['quantity']; ?> <?php echo $item['unit']; ?></td>
                                            <td>₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Order Status Update -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-warning">
                            <h5 class="mb-0">Update Order Status</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Current Status:</strong></label>
                                    <?php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'processing' => 'info',
                                        'shipped' => 'primary',
                                        'delivered' => 'success',
                                        'cancelled' => 'danger'
                                    ];
                                    $color = $statusColors[$orderDetails['status']] ?? 'secondary';
                                    ?>
                                    <div>
                                        <span class="badge bg-<?php echo $color; ?> fs-6">
                                            <?php echo ucfirst($orderDetails['status']); ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="status" class="form-label"><strong>Change Status To:</strong></label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="pending" <?php echo $orderDetails['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="processing" <?php echo $orderDetails['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                        <option value="shipped" <?php echo $orderDetails['status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                        <option value="delivered" <?php echo $orderDetails['status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                        <option value="cancelled" <?php echo $orderDetails['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                </div>

                                <button type="submit" name="update_status" class="btn btn-primary w-100">
                                    <i class="fas fa-save"></i> Update Status
                                </button>
                            </form>

                            <hr class="my-4">

                            <div class="alert alert-info">
                                <h6>Status Guide:</h6>
                                <ul class="small mb-0">
                                    <li><strong>Pending:</strong> Order received</li>
                                    <li><strong>Processing:</strong> Preparing order</li>
                                    <li><strong>Shipped:</strong> Out for delivery</li>
                                    <li><strong>Delivered:</strong> Order completed</li>
                                    <li><strong>Cancelled:</strong> Order cancelled</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<style>
.sidebar {
    position: fixed;
    top: 76px;
    bottom: 0;
    left: 0;
    z-index: 100;
    padding: 0;
    box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
}

.sidebar-sticky {
    position: relative;
    top: 0;
    height: calc(100vh - 76px);
    padding-top: .5rem;
    overflow-x: hidden;
    overflow-y: auto;
}

.sidebar .nav-link {
    font-weight: 500;
    color: #333;
    padding: 10px 20px;
}

.sidebar .nav-link.active {
    color: #28a745;
    background-color: rgba(40, 167, 69, 0.1);
    border-left: 3px solid #28a745;
}

.sidebar .nav-link:hover {
    color: #28a745;
    background-color: rgba(40, 167, 69, 0.05);
}

main {
    margin-left: 16.666667%;
}
</style>

<?php require_once '../includes/footer.php'; ?>
