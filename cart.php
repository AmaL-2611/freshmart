<?php
require_once 'includes/init.php';

// Initialize Cart class
$cart = new Cart($db);
$cartItems = $cart->getItems();
$cartTotal = $cart->getTotal();

$pageTitle = 'Shopping Cart - FreshMart';
require_once 'includes/header.php';
?>

<div class="container my-5">
    <h2 class="mb-4">Shopping Cart</h2>

    <?php if (empty($cartItems)): ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-shopping-cart fa-3x mb-3"></i>
            <h4>Your cart is empty</h4>
            <p>Add some products to your cart to continue shopping</p>
            <a href="index.php" class="btn btn-primary">Continue Shopping</a>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cartItems as $item): ?>
                                    <tr data-product-id="<?php echo $item['id']; ?>">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if ($item['image']): ?>
                                                    <img src="<?php echo $base_url; ?>assets/images/products/<?php echo htmlspecialchars($item['image']); ?>" 
                                                         alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                                         style="width: 60px; height: 60px; object-fit: cover;" 
                                                         class="me-3">
                                                <?php else: ?>
                                                    <div style="width: 60px; height: 60px; background: #ddd;" class="me-3"></div>
                                                <?php endif; ?>
                                                <div>
                                                    <h6 class="mb-0"><?php echo htmlspecialchars($item['name']); ?></h6>
                                                    <small class="text-muted">Stock: <?php echo $item['stock']; ?> <?php echo $item['unit']; ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle">₹<?php echo number_format($item['price'], 2); ?></td>
                                        <td class="align-middle">
                                            <div class="input-group" style="width: 130px;">
                                                <button class="btn btn-outline-secondary btn-sm decrease-qty" type="button">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <input type="number" class="form-control form-control-sm text-center quantity-input" 
                                                       value="<?php echo $item['quantity']; ?>" 
                                                       min="1" 
                                                       max="<?php echo $item['stock']; ?>">
                                                <button class="btn btn-outline-secondary btn-sm increase-qty" type="button">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="align-middle item-total">₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                        <td class="align-middle">
                                            <button class="btn btn-danger btn-sm remove-item" data-id="<?php echo $item['id']; ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Continue Shopping
                </a>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span id="subtotal">₹<?php echo number_format($cartTotal, 2); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Delivery Fee:</span>
                            <span>₹50.00</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong>
                            <strong id="total">₹<?php echo number_format($cartTotal + 50, 2); ?></strong>
                        </div>
                        <a href="checkout.php" class="btn btn-success w-100">
                            <i class="fas fa-lock"></i> Proceed to Checkout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update quantity
    document.querySelectorAll('.increase-qty, .decrease-qty').forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            const input = row.querySelector('.quantity-input');
            const productId = row.dataset.productId;
            let quantity = parseInt(input.value);
            const max = parseInt(input.max);

            if (this.classList.contains('increase-qty')) {
                if (quantity < max) quantity++;
            } else {
                if (quantity > 1) quantity--;
            }

            input.value = quantity;
            updateCart(productId, quantity);
        });
    });

    // Manual quantity input
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            const row = this.closest('tr');
            const productId = row.dataset.productId;
            let quantity = parseInt(this.value);
            const max = parseInt(this.max);

            if (quantity < 1) quantity = 1;
            if (quantity > max) quantity = max;

            this.value = quantity;
            updateCart(productId, quantity);
        });
    });

    // Remove item
    document.querySelectorAll('.remove-item').forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('Remove this item from cart?')) {
                const productId = this.dataset.id;
                removeFromCart(productId);
            }
        });
    });

    function updateCart(productId, quantity) {
        fetch('cart-actions.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=update&product_id=${productId}&quantity=${quantity}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        });
    }

    function removeFromCart(productId) {
        fetch('cart-actions.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=remove&product_id=${productId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        });
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
