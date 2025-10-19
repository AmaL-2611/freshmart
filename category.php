<?php
require_once 'includes/init.php';

// Get category from URL
$categoryName = $_GET['cat'] ?? '';

// Initialize Product class
$productObj = new Product($db);

// Get all categories
$categories = $productObj->getAllCategories();

// Find category ID by name
$categoryId = null;
$categoryTitle = 'All Products';

foreach ($categories as $cat) {
    if (strtolower($cat['name']) === strtolower($categoryName)) {
        $categoryId = $cat['id'];
        $categoryTitle = $cat['name'];
        break;
    }
}

// Get products by category
if ($categoryId) {
    $stmt = $db->prepare("
        SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.category_id = ? AND p.is_active = 1 
        ORDER BY p.created_at DESC
    ");
    $stmt->execute([$categoryId]);
    $products = $stmt->fetchAll();
} else {
    // Show all products if no category selected
    $products = $productObj->getAllProducts();
    $categoryTitle = 'All Products';
}

$pageTitle = $categoryTitle . ' - FreshMart';
require_once 'includes/header.php';
?>

<!-- Breadcrumb -->
<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($categoryTitle); ?></li>
        </ol>
    </nav>
</div>

<!-- Category Header -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-0"><?php echo htmlspecialchars($categoryTitle); ?></h1>
                <p class="text-muted mb-0"><?php echo count($products); ?> products found</p>
            </div>
            <div class="col-md-4 text-end">
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown">
                        <i class="fas fa-sort"></i> Sort By
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="?cat=<?php echo urlencode($categoryName); ?>&sort=name_asc">Name (A-Z)</a></li>
                        <li><a class="dropdown-item" href="?cat=<?php echo urlencode($categoryName); ?>&sort=name_desc">Name (Z-A)</a></li>
                        <li><a class="dropdown-item" href="?cat=<?php echo urlencode($categoryName); ?>&sort=price_asc">Price (Low to High)</a></li>
                        <li><a class="dropdown-item" href="?cat=<?php echo urlencode($categoryName); ?>&sort=price_desc">Price (High to Low)</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Products Grid -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <?php if (empty($products)): ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                    <h3>No products found in this category</h3>
                    <p class="text-muted">Check back later for new products</p>
                    <a href="index.php" class="btn btn-primary">Back to Home</a>
                </div>
            <?php else: ?>
                <?php
                // Apply sorting
                $sort = $_GET['sort'] ?? '';
                switch ($sort) {
                    case 'name_asc':
                        usort($products, function($a, $b) {
                            return strcmp($a['name'], $b['name']);
                        });
                        break;
                    case 'name_desc':
                        usort($products, function($a, $b) {
                            return strcmp($b['name'], $a['name']);
                        });
                        break;
                    case 'price_asc':
                        usort($products, function($a, $b) {
                            return $a['price'] <=> $b['price'];
                        });
                        break;
                    case 'price_desc':
                        usort($products, function($a, $b) {
                            return $b['price'] <=> $a['price'];
                        });
                        break;
                }
                ?>
                
                <?php foreach ($products as $product): ?>
                    <div class="col-md-3 mb-4">
                        <div class="card product-card h-100">
                            <?php if ($product['image']): ?>
                                <img src="assets/images/products/<?php echo htmlspecialchars($product['image']); ?>" 
                                     class="card-img-top" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                                     style="height: 200px; object-fit: cover;">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add to cart functionality
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.id;
            const originalText = this.innerHTML;
            
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
                    this.innerHTML = '<i class="fas fa-check"></i> Added!';
                    this.classList.remove('btn-primary');
                    this.classList.add('btn-success');
                    
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
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
});
</script>

<style>
.product-card {
    transition: transform 0.3s, box-shadow 0.3s;
    border: 1px solid #ddd;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.price {
    font-size: 1.2rem;
    font-weight: bold;
    color: #28a745;
}
</style>

<?php require_once 'includes/footer.php'; ?>
