<?php
require_once 'includes/init.php';
require_once 'includes/header.php';
?>

<!-- Hero Section -->
<div class="hero" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); min-height: 400px; display: flex; align-items: center;">
    <div class="container">
        <div class="hero-content text-white">
            <h1 class="display-4 fw-bold mb-3">Fresh Groceries Delivered to Your Doorstep</h1>
            <p class="lead mb-4">Shop the freshest produce, dairy, and pantry staples with just a few clicks.</p>
            <a href="category.php" class="btn btn-light btn-lg">Shop Now</a>
        </div>
    </div>
</div>

<!-- Featured Categories -->
<section class="categories py-5">
    <div class="container">
        <h2 class="text-center mb-5">Shop by Category</h2>
        <div class="row">
            <div class="col-md-3 mb-4">
                <a href="category.php?cat=fruits" class="text-decoration-none">
                    <div class="category-card" style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%); min-height: 200px; display: flex; align-items: center; justify-content: center; border-radius: 10px;">
                        <h3 class="text-white">🍎 Fresh Fruits</h3>
                    </div>
                </a>
            </div>
            <div class="col-md-3 mb-4">
                <a href="category.php?cat=vegetables" class="text-decoration-none">
                    <div class="category-card" style="background: linear-gradient(135deg, #51cf66 0%, #37b24d 100%); min-height: 200px; display: flex; align-items: center; justify-content: center; border-radius: 10px;">
                        <h3 class="text-white">🥕 Fresh Vegetables</h3>
                    </div>
                </a>
            </div>
            <div class="col-md-3 mb-4">
                <a href="category.php?cat=dairy" class="text-decoration-none">
                    <div class="category-card" style="background: linear-gradient(135deg, #74c0fc 0%, #4dabf7 100%); min-height: 200px; display: flex; align-items: center; justify-content: center; border-radius: 10px;">
                        <h3 class="text-white">🥛 Dairy & Eggs</h3>
                    </div>
                </a>
            </div>
            <div class="col-md-3 mb-4">
                <a href="category.php?cat=bakery" class="text-decoration-none">
                    <div class="category-card" style="background: linear-gradient(135deg, #ffd43b 0%, #fab005 100%); min-height: 200px; display: flex; align-items: center; justify-content: center; border-radius: 10px;">
                        <h3 class="text-white">🍞 Bakery</h3>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="featured-products py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5">Featured Products</h2>
        <div class="row">
            <?php
            // Get featured products from database
            $productObj = new Product($db);
            $featuredProducts = $productObj->getFeaturedProducts(8);

            if (empty($featuredProducts)):
            ?>
                <div class="col-12 text-center">
                    <p class="text-muted">No featured products available at the moment.</p>
                </div>
            <?php else: ?>
                <?php foreach ($featuredProducts as $product): ?>
                    <div class="col-md-3 mb-4">
                        <div class="card product-card h-100">
                            <?php if ($product['image']): ?>
                                <img src="../assets/images\products/<?php echo htmlspecialchars($product['image']); ?>" 
                                     class="card-img-top" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                                     loading="lazy"
                                     style="height: 200px; object-fit: cover;"
                                     onerror="this.onerror=null; this.parentElement.innerHTML='<div style=\'height: 200px; background: #f0f0f0; display: flex; align-items: center; justify-content: center;\'><i class=\'fas fa-image fa-3x text-muted\'></i></div>';">
                            <?php else: ?>
                                <div style="height: 200px; background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                            <?php endif; ?>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                <?php if ($product['description']): ?>
                                    <p class="card-text text-muted small"><?php echo htmlspecialchars(substr($product['description'], 0, 60)) . '...'; ?></p>
                                <?php endif; ?>
                                <div class="mt-auto">
                                    <p class="price mb-2">₹<?php echo number_format($product['price'], 2); ?> / <?php echo htmlspecialchars($product['unit']); ?></p>
                                    <?php if ($product['stock'] > 0): ?>
                                        <button class="btn btn-primary btn-sm w-100 add-to-cart" data-id="<?php echo $product['id']; ?>">
                                            <i class="fas fa-shopping-cart"></i> Add to Cart
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-secondary btn-sm w-100" disabled>
                                            Out of Stock
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="why-choose-us py-5">
    <div class="container">
        <h2 class="text-center mb-5">Why Choose Our Store</h2>
        <div class="row text-center">
            <div class="col-md-4 mb-4">
                <div class="feature-box">
                    <i class="fas fa-truck"></i>
                    <h3>Fast Delivery</h3>
                    <p>Get your groceries delivered to your doorstep within 2 hours</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-box">
                    <i class="fas fa-leaf"></i>
                    <h3>Fresh Products</h3>
                    <p>100% fresh and high-quality products</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-box">
                    <i class="fas fa-lock"></i>
                    <h3>Secure Payment</h3>
                    <p>Safe and secure payment options</p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add to cart functionality
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.id;
            const originalText = this.innerHTML;
            
            // Disable button and show loading
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
            
            fetch('cart-actions.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=add&product_id=${productId}&quantity=1`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    this.innerHTML = '<i class="fas fa-check"></i> Added!';
                    this.classList.remove('btn-primary');
                    this.classList.add('btn-success');
                    
                    // Update cart count in header
                    updateCartCount();
                    
                    // Reset button after 2 seconds
                    setTimeout(() => {
                        this.innerHTML = originalText;
                        this.classList.remove('btn-success');
                        this.classList.add('btn-primary');
                        this.disabled = false;
                    }, 2000);
                } else {
                    alert(data.message);
                    this.innerHTML = originalText;
                    this.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to add product to cart');
                this.innerHTML = originalText;
                this.disabled = false;
            });
        });
    });
    
    function updateCartCount() {
        // Reload page to update cart count (you can make this more dynamic with AJAX)
        location.reload();
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
