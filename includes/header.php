<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Define base URL - detect if we're in admin folder or root
$base_url = (basename(dirname($_SERVER['PHP_SELF'])) === 'admin') ? '../' : './';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FreshMart - Your Online Grocery Store</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-success" href="<?php echo $base_url; ?>index.php">FreshMart</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="categoriesDropdown" role="button" data-bs-toggle="dropdown">
                            Categories
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>category.php?cat=fruits">Fruits</a></li>
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>category.php?cat=vegetables">Vegetables</a></li>
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>category.php?cat=dairy">Dairy</a></li>
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>category.php?cat=bakery">Bakery</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_url; ?>about.php">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_url; ?>contact.php">Contact</a>
                    </li>
                </ul>
                
                <div class="d-flex align-items-center">
                    <form action="<?php echo $base_url; ?>search.php" method="GET" class="me-3">
                        <div class="input-group">
                            <input type="text" class="form-control" name="q" placeholder="Search products..." required>
                            <button class="btn btn-outline-success" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                    
                    <div class="dropdown me-3">
                        <a href="#" class="text-dark" id="accountDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user fa-lg"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php if(isset($_SESSION['user_id'])): ?>
                                <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                                    <li><a class="dropdown-item" href="<?php echo $base_url; ?>admin/admin.php"><i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="<?php echo $base_url; ?>index.php"><i class="fas fa-home me-2"></i>Home</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="<?php echo $base_url; ?>logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            <?php else: ?>
                                <li><a class="dropdown-item" href="<?php echo $base_url; ?>login.php">Login</a></li>
                                <li><a class="dropdown-item" href="<?php echo $base_url; ?>register.php">Register</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    
                    <a href="<?php echo $base_url; ?>cart.php" class="text-dark position-relative">
                        <i class="fas fa-shopping-cart fa-lg"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success">
                            <?php 
                            $cartCount = 0;
                            if (isset($_SESSION['cart'])) {
                                foreach ($_SESSION['cart'] as $item) {
                                    $cartCount += $item['quantity'];
                                }
                            }
                            echo $cartCount;
                            ?>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </nav>
