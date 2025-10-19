<?php
class Order {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Get all orders
    public function getAllOrders($limit = null, $offset = 0) {
        $sql = "SELECT o.*, u.name as customer_name, u.email as customer_email 
                FROM orders o 
                LEFT JOIN users u ON o.user_id = u.id 
                ORDER BY o.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        
        $stmt = $this->db->prepare($sql);
        
        if ($limit) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Get order by ID
    public function getOrderById($orderId) {
        $stmt = $this->db->prepare("
            SELECT o.*, u.name as customer_name, u.email as customer_email 
            FROM orders o 
            LEFT JOIN users u ON o.user_id = u.id 
            WHERE o.id = ?
        ");
        $stmt->execute([$orderId]);
        return $stmt->fetch();
    }

    // Get order items
    public function getOrderItems($orderId) {
        $stmt = $this->db->prepare("
            SELECT oi.*, p.name as product_name, p.image as product_image, p.unit 
            FROM order_items oi 
            LEFT JOIN products p ON oi.product_id = p.id 
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }

    // Update order status
    public function updateOrderStatus($orderId, $status) {
        $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        
        if (!in_array($status, $validStatuses)) {
            return ['success' => false, 'message' => 'Invalid status'];
        }
        
        $stmt = $this->db->prepare("UPDATE orders SET status = ? WHERE id = ?");
        
        if ($stmt->execute([$status, $orderId])) {
            return ['success' => true, 'message' => 'Order status updated successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to update order status'];
        }
    }

    // Get orders by user
    public function getOrdersByUser($userId) {
        $stmt = $this->db->prepare("
            SELECT * FROM orders 
            WHERE user_id = ? 
            ORDER BY created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    // Get order statistics
    public function getOrderStats() {
        $stats = [];
        
        // Total orders
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM orders");
        $stats['total_orders'] = $stmt->fetch()['total'];
        
        // Pending orders
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM orders WHERE status = 'pending'");
        $stats['pending_orders'] = $stmt->fetch()['total'];
        
        // Total revenue
        $stmt = $this->db->query("SELECT SUM(total_amount) as total FROM orders WHERE status != 'cancelled'");
        $stats['total_revenue'] = $stmt->fetch()['total'] ?? 0;
        
        return $stats;
    }
}
?>
