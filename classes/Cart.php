<?php
class Cart {
    private $db;

    public function __construct($db) {
        $this->db = $db;
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    // Add item to cart
    public function addItem($productId, $quantity = 1) {
        // Get product details
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = ? AND is_active = 1");
        $stmt->execute([$productId]);
        $product = $stmt->fetch();

        if (!$product) {
            return ['success' => false, 'message' => 'Product not found'];
        }

        // Check stock availability
        $currentCartQty = isset($_SESSION['cart'][$productId]) ? $_SESSION['cart'][$productId]['quantity'] : 0;
        $totalQty = $currentCartQty + $quantity;

        if ($totalQty > $product['stock']) {
            return ['success' => false, 'message' => 'Insufficient stock available'];
        }

        // Add or update cart item
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$productId] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => $quantity,
                'image' => $product['image'],
                'unit' => $product['unit'],
                'stock' => $product['stock']
            ];
        }

        return ['success' => true, 'message' => 'Product added to cart'];
    }

    // Update cart item quantity
    public function updateQuantity($productId, $quantity) {
        if (!isset($_SESSION['cart'][$productId])) {
            return ['success' => false, 'message' => 'Product not in cart'];
        }

        // Get product stock
        $stmt = $this->db->prepare("SELECT stock FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch();

        if ($quantity > $product['stock']) {
            return ['success' => false, 'message' => 'Insufficient stock available'];
        }

        if ($quantity <= 0) {
            unset($_SESSION['cart'][$productId]);
        } else {
            $_SESSION['cart'][$productId]['quantity'] = $quantity;
        }

        return ['success' => true, 'message' => 'Cart updated'];
    }

    // Remove item from cart
    public function removeItem($productId) {
        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
            return ['success' => true, 'message' => 'Product removed from cart'];
        }
        return ['success' => false, 'message' => 'Product not in cart'];
    }

    // Get all cart items
    public function getItems() {
        return $_SESSION['cart'] ?? [];
    }

    // Get cart total
    public function getTotal() {
        $total = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }

    // Get cart item count
    public function getItemCount() {
        $count = 0;
        foreach ($_SESSION['cart'] as $item) {
            $count += $item['quantity'];
        }
        return $count;
    }

    // Clear cart
    public function clear() {
        $_SESSION['cart'] = [];
    }

    // Process order and update stock
    public function processOrder($userId, $shippingAddress, $paymentMethod) {
        try {
            $this->db->beginTransaction();

            // Calculate total
            $total = $this->getTotal();

            // Create order
            $stmt = $this->db->prepare("
                INSERT INTO orders (user_id, total_amount, shipping_address, payment_method, status) 
                VALUES (?, ?, ?, ?, 'pending')
            ");
            $stmt->execute([$userId, $total, $shippingAddress, $paymentMethod]);
            $orderId = $this->db->lastInsertId();

            // Add order items and update stock
            foreach ($_SESSION['cart'] as $item) {
                // Insert order item
                $stmt = $this->db->prepare("
                    INSERT INTO order_items (order_id, product_id, quantity, price) 
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([$orderId, $item['id'], $item['quantity'], $item['price']]);

                // Update product stock
                $stmt = $this->db->prepare("
                    UPDATE products 
                    SET stock = stock - ? 
                    WHERE id = ? AND stock >= ?
                ");
                $stmt->execute([$item['quantity'], $item['id'], $item['quantity']]);

                // Check if stock was updated
                if ($stmt->rowCount() == 0) {
                    throw new Exception('Insufficient stock for ' . $item['name']);
                }
            }

            $this->db->commit();
            $this->clear();

            return ['success' => true, 'message' => 'Order placed successfully', 'order_id' => $orderId];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
?>
