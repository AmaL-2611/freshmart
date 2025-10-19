# FreshMart - Online Grocery Store

A complete e-commerce web application for online grocery shopping built with PHP, MySQL, and Bootstrap.

## 🚀 Features

### Customer Features
- 🛒 **Shopping Cart** - Add products, update quantities, remove items
- 💳 **Payment Integration** - Razorpay payment gateway (UPI, Cards, Wallets)
- 💰 **Cash on Delivery** - COD payment option
- 🔍 **Product Search** - Search products by name or description
- 📦 **Category Filtering** - Browse products by categories
- 📊 **Product Sorting** - Sort by name or price
- 👤 **User Authentication** - Register, Login, Logout
- 📱 **Responsive Design** - Works on all devices

### Admin Features
- 📊 **Admin Dashboard** - Overview of orders, products, revenue
- ➕ **Product Management** - Add, edit, delete products
- 📦 **Order Management** - View and update order status
- 📈 **Real-time Statistics** - Track sales and inventory
- 🖼️ **Image Upload** - Upload product images
- 📋 **Stock Management** - Automatic stock updates after orders

## 🛠️ Technologies Used

- **Backend:** PHP 8.x
- **Database:** MySQL
- **Frontend:** HTML5, CSS3, JavaScript
- **Framework:** Bootstrap 5
- **Icons:** Font Awesome
- **Payment:** Razorpay API
- **Architecture:** OOP (Object-Oriented Programming)

## 📋 Prerequisites

- XAMPP (or any PHP server)
- PHP 8.0 or higher
- MySQL 5.7 or higher
- Web browser

## 🔧 Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/freshmart.git
   cd freshmart
   ```

2. **Move to XAMPP htdocs**
   ```bash
   # Copy the project to C:\xampp\htdocs\anki
   ```

3. **Start XAMPP**
   - Start Apache
   - Start MySQL

4. **Create Database**
   - Go to: `http://localhost/anki/setup-database.php`
   - This will automatically create all tables and sample data

5. **Configure Razorpay (Optional)**
   - Sign up at [Razorpay](https://razorpay.com/)
   - Get your Test API keys
   - Update `config/razorpay.php` with your keys

6. **Access the Application**
   - Homepage: `http://localhost/anki/`
   - Admin Panel: `http://localhost/anki/admin/admin.php`

## 👥 Default Credentials

### Admin Account
- **Email:** freshmart@gmail.com
- **Password:** admin123

### Test User Account
- **Email:** test@example.com
- **Password:** Test@123

## 📁 Project Structure

```
anki/
├── admin/                  # Admin panel
│   ├── admin.php          # Dashboard
│   ├── add-product.php    # Add products
│   ├── orders.php         # Order management
│   └── order-details.php  # Order details
├── assets/                # Static files
│   ├── css/              # Stylesheets
│   └── images/           # Images
├── classes/              # OOP Classes
│   ├── User.php         # User management
│   ├── Product.php      # Product management
│   ├── Cart.php         # Shopping cart
│   └── Order.php        # Order management
├── config/              # Configuration
│   └── razorpay.php    # Payment config
├── database/           # Database files
│   └── schema.sql     # Database schema
├── includes/          # Common files
│   ├── init.php      # Initialization
│   ├── header.php    # Header template
│   └── footer.php    # Footer template
├── index.php         # Homepage
├── cart.php          # Shopping cart
├── checkout.php      # Checkout page
├── search.php        # Search results
├── category.php      # Category page
├── about.php         # About us
├── login.php         # Login page
└── register.php      # Registration page
```

## 🗄️ Database Tables

- `users` - User accounts
- `products` - Product catalog
- `categories` - Product categories
- `orders` - Customer orders
- `order_items` - Order line items
- `payment_transactions` - Payment records
- `user_sessions` - User sessions

## 🔐 Security Features

- Password hashing (bcrypt)
- CSRF token protection
- SQL injection prevention (PDO prepared statements)
- XSS protection (htmlspecialchars)
- Session management
- Input validation

## 💳 Payment Integration

The application supports two payment methods:

1. **Razorpay** - Online payments (UPI, Cards, Wallets)
2. **Cash on Delivery** - Pay on delivery

### Test Payment Credentials
- **Card Number:** 4111 1111 1111 1111
- **CVV:** Any 3 digits
- **Expiry:** Any future date
- **UPI:** success@razorpay

## 📱 Features Walkthrough

### For Customers:
1. Browse products by category
2. Search for specific products
3. Add items to cart
4. Proceed to checkout
5. Choose payment method
6. Place order
7. View order confirmation

### For Admin:
1. Login to admin panel
2. View dashboard statistics
3. Add new products with images
4. Manage product inventory
5. View and manage orders
6. Update order status
7. Track revenue

## 🐛 Troubleshooting

**Database Connection Error:**
- Check if MySQL is running in XAMPP
- Verify database credentials in `includes/config.php`

**Payment Not Working:**
- Ensure Razorpay API keys are correct
- Check if you're using test mode keys

**Images Not Loading:**
- Create `assets/images/products/` folder
- Check folder permissions

## 📝 License

This project is open source and available under the [MIT License](LICENSE).

## 👨‍💻 Author

Your Name - [Your GitHub Profile](https://github.com/yourusername)

## 🤝 Contributing

Contributions, issues, and feature requests are welcome!

## ⭐ Show your support

Give a ⭐️ if you like this project!

## 📧 Contact

For any queries, reach out at: your.email@example.com
