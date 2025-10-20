<?php
/**
 * Database Setup Script
 * Run this file once to create the database and tables
 * Access: http://localhost./setup-database.php
 */

// Database connection settings
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Default XAMPP password is empty
define('DB_NAME', 'freshmart_db');

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Database Setup - FreshMart</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body>
    <div class='container mt-5'>
        <div class='row justify-content-center'>
            <div class='col-md-8'>
                <div class='card'>
                    <div class='card-header bg-success text-white'>
                        <h3 class='mb-0'>FreshMart Database Setup</h3>
                    </div>
                    <div class='card-body'>";

try {
    // Connect to MySQL server (without database)
    $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<div class='alert alert-info'>✓ Connected to MySQL server successfully!</div>";
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
    echo "<div class='alert alert-success'>✓ Database '" . DB_NAME . "' created successfully!</div>";
    
    // Use the database
    $pdo->exec("USE " . DB_NAME);
    
    // Create users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        phone VARCHAR(20),
        address TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $pdo->exec($sql);
    echo "<div class='alert alert-success'>✓ Table 'users' created successfully!</div>";
    
    // Create user_sessions table
    $sql = "CREATE TABLE IF NOT EXISTS user_sessions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        token VARCHAR(255) NOT NULL,
        ip_address VARCHAR(45) NOT NULL,
        user_agent TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        expires_at TIMESTAMP NULL DEFAULT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $pdo->exec($sql);
    echo "<div class='alert alert-success'>✓ Table 'user_sessions' created successfully!</div>";
    
    // Add role column to users table
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS role ENUM('user', 'admin') DEFAULT 'user' AFTER email");
    echo "<div class='alert alert-success'>✓ Added 'role' column to users table!</div>";
    
    // Create categories table
    $sql = "CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL UNIQUE,
        description TEXT,
        image VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $pdo->exec($sql);
    echo "<div class='alert alert-success'>✓ Table 'categories' created successfully!</div>";
    
    // Insert default categories
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM categories");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $sql = "INSERT INTO categories (name, description, image) VALUES 
                ('Fruits', 'Fresh and organic fruits', 'fruits.jpg'),
                ('Vegetables', 'Farm fresh vegetables', 'vegetables.jpg'),
                ('Dairy', 'Milk, cheese, and dairy products', 'dairy.jpg'),
                ('Bakery', 'Fresh bread and bakery items', 'bakery.jpg')";
        $pdo->exec($sql);
        echo "<div class='alert alert-success'>✓ Default categories added!</div>";
    }
    
    // Create products table
    $sql = "CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(200) NOT NULL,
        description TEXT,
        price DECIMAL(10, 2) NOT NULL,
        category_id INT NOT NULL,
        image VARCHAR(255),
        stock INT DEFAULT 0,
        unit VARCHAR(50) DEFAULT 'piece',
        is_featured BOOLEAN DEFAULT FALSE,
        is_active BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $pdo->exec($sql);
    echo "<div class='alert alert-success'>✓ Table 'products' created successfully!</div>";
    
    // Insert sample products
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $sql = "INSERT INTO products (name, description, price, category_id, image, stock, unit, is_featured) VALUES 
                ('Fresh Apples', 'Crisp and sweet red apples', 2.99, 1, 'apple.jpg', 100, 'kg', TRUE),
                ('Organic Bananas', 'Fresh organic bananas', 1.99, 1, 'banana.jpg', 150, 'dozen', TRUE),
                ('Fresh Carrots', 'Crunchy orange carrots', 0.99, 2, 'carrot.jpg', 80, 'kg', TRUE),
                ('Fresh Milk', 'Full cream fresh milk', 3.49, 3, 'milk.jpg', 50, 'liter', TRUE)";
        $pdo->exec($sql);
        echo "<div class='alert alert-success'>✓ Sample products added!</div>";
    }
    
    // Create orders table
    $sql = "CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        total_amount DECIMAL(10, 2) NOT NULL,
        shipping_address TEXT NOT NULL,
        payment_method VARCHAR(50) NOT NULL,
        status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $pdo->exec($sql);
    echo "<div class='alert alert-success'>✓ Table 'orders' created successfully!</div>";
    
    // Create order_items table
    $sql = "CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL,
        price DECIMAL(10, 2) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $pdo->exec($sql);
    echo "<div class='alert alert-success'>✓ Table 'order_items' created successfully!</div>";
    
    // Create payment_transactions table
    $sql = "CREATE TABLE IF NOT EXISTS payment_transactions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        payment_id VARCHAR(255) NOT NULL,
        payment_method VARCHAR(50) NOT NULL,
        amount DECIMAL(10, 2) NOT NULL,
        status ENUM('pending', 'success', 'failed') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $pdo->exec($sql);
    echo "<div class='alert alert-success'>✓ Table 'payment_transactions' created successfully!</div>";
    
    // Check if test user already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = 'test@example.com'");
    $stmt->execute();
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        // Insert test user (password: Test@123)
        $sql = "INSERT INTO users (name, email, password) VALUES 
                ('Test User', 'test@example.com', '$2y$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi')";
        $pdo->exec($sql);
        echo "<div class='alert alert-success'>✓ Test user created successfully!<br>
              <strong>Email:</strong> test@example.com<br>
              <strong>Password:</strong> Test@123</div>";
    } else {
        echo "<div class='alert alert-info'>ℹ Test user already exists.</div>";
    }
    
    // Create admin user
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = 'freshmart@gmail.com'");
    $stmt->execute();
    $adminCount = $stmt->fetchColumn();
    
    if ($adminCount == 0) {
        // Insert admin user (password: admin123)
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (name, email, role, password) VALUES 
                ('Admin', 'freshmart@gmail.com', 'admin', ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$hashedPassword]);
        echo "<div class='alert alert-success'>✓ Admin user created successfully!<br>
              <strong>Email:</strong> freshmart@gmail.com<br>
              <strong>Password:</strong> admin123</div>";
    } else {
        // Update existing admin user role and password
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET role = 'admin', password = ? WHERE email = 'freshmart@gmail.com'");
        $stmt->execute([$hashedPassword]);
        echo "<div class='alert alert-info'>ℹ Admin user already exists and updated.</div>";
    }
    
    echo "<div class='alert alert-success mt-4'>
            <h4>✓ Database setup completed successfully!</h4>
            <p class='mb-0'>You can now use the application.</p>
          </div>";
    
    echo "<div class='mt-3'>
            <a href='index.php' class='btn btn-success me-2'>Go to Homepage</a>
            <a href='login.php' class='btn btn-primary me-2'>Go to Login</a>
            <a href='register.php' class='btn btn-secondary'>Go to Register</a>
          </div>";
    
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>
            <h4>Error!</h4>
            <p>" . htmlspecialchars($e->getMessage()) . "</p>
            <hr>
            <p><strong>Please make sure:</strong></p>
            <ul>
                <li>XAMPP Apache and MySQL services are running</li>
                <li>MySQL is accessible on localhost:3306</li>
                <li>The database user has proper permissions</li>
            </ul>
          </div>";
}

echo "          </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>";
?>
