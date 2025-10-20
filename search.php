<?php
require_once 'includes/init.php';

// Get search query
$searchQuery = trim($_GET['q'] ?? '');

// Initialize Product class
$productObj = new Product($db);

$products = [];
$searchPerformed = false;

if (!empty($searchQuery)) {
    $searchPerformed = true;
    
    // Search products by name or description
    $stmt = $db->prepare("
        SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE (p.name LIKE ? OR p.description LIKE ?) 
        AND p.is_active = 1 
        ORDER BY p.name ASC
    ");
    
    $searchTerm = '%' . $searchQuery . '%';
    $stmt->execute([$searchTerm, $searchTerm]);
    $products = $stmt->fetchAll();
}

$pageTitle = 'Search Results - FreshMart';
require_once 'includes/header.php';
?>

<!-- Breadcrumb -->
<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Search Results</li>
        </ol>
    </nav>
</div>

<!-- Search Header -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1 class="mb-3">Search Results</h1>
                <?php if ($searchPerformed): ?>
                    <p class="text-muted">
                        <?php if (!empty($products)): ?>
                            Found <strong><?php echo count($products); ?></strong> product(s) for "<strong><?php echo htmlspecialchars($searchQuery); ?></strong>"
                        <?php else: ?>
                            No results found for "<strong><?php echo htmlspecialchars($searchQuery); ?></strong>"
                        <?php endif; ?>
                    </p>
                <?php else: ?>
                    <p class="text-muted">Please enter a search term</p>
                <?php endif; ?>
                
                <!-- Search Form -->
                <form method="GET" action="search.php" class="mt-3">
                    <div class="input-group" style="max-width: 500px;">
                        <input type="text" 
                               class="form-control" 
                               name="q" 
                               placeholder="Search products..." 
                               value="<?php echo htmlspecialchars($searchQuery); ?>"
                               required>
                        <button class="btn btn-success" type="submit">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Search Results -->
<section class="py-5">
    <div class="container">
        <?php if ($searchPerformed): ?>
            <?php if (empty($products)): ?>
                <div class="row">
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-search fa-4x text-muted mb-3"></i>
                        <h3>No products found</h3>
                        <p class="text-muted">Try searching with different keywords</p>
                        <a href="index.php" class="btn btn-success mt-3">Back to Home</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($products as $product): ?>
                        <div class="col-md-3 mb-4">
                            <div class="card product-card h-100">
                                <?php if ($product['image']): ?>
                                    <img src="<?php echo $base_url; ?>assets/images/products/<?php echo htmlspecialchars($product['image']); ?>" 
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
                                    <span class="badge bg-success mb-2" style="width: fit-content;">
                                        <?php echo htmlspecialchars($product['category_name']); ?>
                                    </span>
                                    <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                    <?php if ($product['description']): ?>
                                        <p class="card-text text-muted small">
                                            <?php echo htmlspecialchars(substr($product['description'], 0, 80)) . '...'; ?>
                                        </p>
                                    <?php endif; ?>
                                    <div class="mt-auto">
                                        <p class="price mb-2">₹<?php echo number_format($product['price'], 2); ?> / <?php echo htmlspecialchars($product['unit']); ?></p>
                                        <?php if ($product['stock'] > 0): ?>
                                            <button class="btn btn-success btn-sm w-100 add-to-cart" data-id="<?php echo $product['id']; ?>">
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
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="row">
                <div class="col-12 text-center py-5">
                    <i class="fas fa-search fa-4x text-muted mb-3"></i>
                    <h3>Start Searching</h3>
                    <p class="text-muted">Enter a product name or keyword to search</p>
                </div>
            </div>
        <?php endif; ?>
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
                    this.classList.remove('btn-success');
                    this.classList.add('btn-primary');
                    
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
