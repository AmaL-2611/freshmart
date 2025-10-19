<?php
class Product {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Get all products
    public function getAllProducts($limit = null, $offset = 0) {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.is_active = 1 
                ORDER BY p.created_at DESC";
        
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

    // Get featured products
    public function getFeaturedProducts($limit = 8) {
        $stmt = $this->db->prepare("
            SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.is_featured = 1 AND p.is_active = 1 
            ORDER BY p.created_at DESC 
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Get product by ID
    public function getProductById($id) {
        $stmt = $this->db->prepare("
            SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Add new product
    public function addProduct($data) {
        $stmt = $this->db->prepare("
            INSERT INTO products (name, description, price, category_id, image, stock, unit, is_featured, is_active) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['name'],
            $data['description'],
            $data['price'],
            $data['category_id'],
            $data['image'] ?? null,
            $data['stock'] ?? 0,
            $data['unit'] ?? 'piece',
            $data['is_featured'] ?? 0,
            $data['is_active'] ?? 1
        ]);
    }

    // Update product
    public function updateProduct($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE products 
            SET name = ?, description = ?, price = ?, category_id = ?, 
                image = ?, stock = ?, unit = ?, is_featured = ?, is_active = ? 
            WHERE id = ?
        ");
        
        return $stmt->execute([
            $data['name'],
            $data['description'],
            $data['price'],
            $data['category_id'],
            $data['image'] ?? null,
            $data['stock'] ?? 0,
            $data['unit'] ?? 'piece',
            $data['is_featured'] ?? 0,
            $data['is_active'] ?? 1,
            $id
        ]);
    }

    // Delete product
    public function deleteProduct($id) {
        $stmt = $this->db->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Get all categories
    public function getAllCategories() {
        $stmt = $this->db->query("SELECT * FROM categories ORDER BY name ASC");
        return $stmt->fetchAll();
    }
}
?>
