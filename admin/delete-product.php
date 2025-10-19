<?php
require_once '../includes/init.php';

// Initialize User class
$user = new User($db);

// Check if user is logged in and is admin
if (!$user->isLoggedIn() || !$user->isAdmin()) {
    header('Location: ../login.php');
    exit();
}

// Check if product ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$productId = (int)$_GET['id'];

// Initialize Product class
$product = new Product($db);

// Get product details
$productDetails = $product->getProductById($productId);

if (!$productDetails) {
    $_SESSION['error'] = 'Product not found.';
    header('Location: index.php');
    exit();
}

// Delete the product
if ($product->deleteProduct($productId)) {
    // Delete product image if exists
    if ($productDetails['image']) {
        $imagePath = '../assets/images/products/' . $productDetails['image'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
    
    $_SESSION['success'] = 'Product deleted successfully!';
} else {
    $_SESSION['error'] = 'Failed to delete product.';
}

header('Location: admin.php');
exit();
?>
