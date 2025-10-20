<?php
require_once '../includes/init.php';

// Initialize User class
$user = new User($db);

// Check if user is logged in and is admin
if (!$user->isLoggedIn() || !$user->isAdmin()) {
    header('Location: ../login.php');
    exit();
}

// Initialize Product and Order classes
$product = new Product($db);
$order = new Order($db);

// Get all products
$products = $product->getAllProducts();
$categories = $product->getAllCategories();

// Get order statistics
$orderStats = $order->getOrderStats();

$pageTitle = 'Admin Dashboard - FreshMart';
require_once '../includes/header.php';
?>

<div class="container-fluid mt-4">
    <!-- Hamburger Menu Button -->
    <button class="btn btn-success d-md-none mb-3" id="sidebarToggle" style="position: fixed; top: 80px; left: 10px; z-index: 1050;">
        <i class="fas fa-bars"></i>
    </button>

    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 d-md-block bg-light sidebar" id="adminSidebar">
            <div class="sidebar-sticky pt-3">
                <h6 class="sidebar-heading px-3 mt-4 mb-1 text-muted">
                    <span>Admin Panel</span>
                </h6>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="admin.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="add-product.php">
                            <i class="fas fa-plus"></i> Add Product
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="orders.php">
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
        <main role="main" class="col-md-10 ml-sm-auto col-lg-10 px-md-4" style="margin-left: auto;">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="add-product.php" class="btn btn-success">
                        <i class="fas fa-plus"></i> Add New Product
                    </a>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <h5 class="card-title">Total Products</h5>
                            <h2><?php echo count($products); ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <h5 class="card-title">Categories</h5>
                            <h2><?php echo count($categories); ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-warning">
                        <div class="card-body">
                            <h5 class="card-title">Total Orders</h5>
                            <h2><?php echo $orderStats['total_orders']; ?></h2>
                            <small>Pending: <?php echo $orderStats['pending_orders']; ?></small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-info">
                        <div class="card-body">
                            <h5 class="card-title">Revenue</h5>
                            <h2>₹<?php echo number_format($orderStats['total_revenue'], 2); ?></h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Table -->
            <h3 class="mb-3">All Products</h3>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Featured</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr>
                                <td colspan="9" class="text-center">No products found. <a href="add-product.php">Add your first product</a></td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($products as $prod): ?>
                                <tr>
                                    <td><?php echo $prod['id']; ?></td>
                                    <td>
                                        <?php if ($prod['image']): ?>
                                            <img src="../assets/images/products/<?php echo htmlspecialchars($prod['image']); ?>" 
                                                 alt="<?php echo htmlspecialchars($prod['name']); ?>" 
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                        <?php else: ?>
                                            <div style="width: 50px; height: 50px; background: #ddd;"></div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($prod['name']); ?></td>
                                    <td><?php echo htmlspecialchars($prod['category_name']); ?></td>
                                    <td>₹<?php echo number_format($prod['price'], 2); ?></td>
                                    <td><?php echo $prod['stock']; ?> <?php echo $prod['unit']; ?></td>
                                    <td>
                                        <?php if ($prod['is_featured']): ?>
                                            <span class="badge bg-success">Yes</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">No</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($prod['is_active']): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="edit-product.php?id=<?php echo $prod['id']; ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="delete-product.php?id=<?php echo $prod['id']; ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Are you sure you want to delete this product?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>

<style>
/* Hide navbar toggle button on admin pages */
.navbar-toggler {
    display: none !important;
}

/* Footer alignment with sidebar */
@media (min-width: 1200px) {
    footer {
        margin-left: 250px;
    }
}

@media (max-width: 1199.98px) {
    footer {
        margin-left: 0;
    }
}

/* Sidebar - Always visible on desktop */
.sidebar {
    position: fixed;
    top: 76px;
    bottom: 0;
    left: 0;
    z-index: 100;
    padding: 0;
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
    background-color: #f8f9fa;
}

/* Large Desktop - Sidebar always visible */
@media (min-width: 1200px) {
    .sidebar {
        width: 250px;
        transform: translateX(0) !important;
    }
    
    main {
        margin-left: 250px !important;
    }
    
    #sidebarToggle {
        display: none !important;
    }
}

/* Tablet, Mobile, and Small Desktop - Hamburger menu */
@media (max-width: 1199.98px) {
    .sidebar {
        width: 250px;
        transform: translateX(-100%);
        transition: transform 0.3s ease-in-out;
        z-index: 1000;
    }
    
    .sidebar.show {
        transform: translateX(0);
    }
    
    main {
        margin-left: 0 !important;
    }
    
    #sidebarToggle {
        display: block !important;
    }
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

/* Overlay for mobile */
.sidebar-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 99;
}

.sidebar-overlay.show {
    display: block;
}

.card {
    border-radius: 10px;
    margin-bottom: 20px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('adminSidebar');
    const body = document.body;
    
    // Create overlay
    const overlay = document.createElement('div');
    overlay.className = 'sidebar-overlay';
    body.appendChild(overlay);
    
    // Toggle sidebar
    sidebarToggle.addEventListener('click', function() {
        sidebar.classList.toggle('show');
        overlay.classList.toggle('show');
    });
    
    // Close sidebar when clicking overlay
    overlay.addEventListener('click', function() {
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
    });
    
    // Close sidebar when clicking a link (mobile)
    const sidebarLinks = sidebar.querySelectorAll('.nav-link');
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth < 768) {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            }
        });
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>
