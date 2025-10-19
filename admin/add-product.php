<?php
require_once '../includes/init.php';

// Initialize User class
$user = new User($db);

// Check if user is logged in and is admin
if (!$user->isLoggedIn() || !$user->isAdmin()) {
    header('Location: ../login.php');
    exit();
}

// Initialize Product class
$product = new Product($db);
$categories = $product->getAllCategories();

$errors = [];
$success = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !$user->validateCSRFToken($_POST['csrf_token'])) {
        $errors[] = 'Invalid request. Please try again.';
    } else {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = trim($_POST['price'] ?? '');
        $category_id = trim($_POST['category_id'] ?? '');
        $stock = trim($_POST['stock'] ?? 0);
        $unit = trim($_POST['unit'] ?? 'piece');
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        // Validation
        if (empty($name)) {
            $errors['name'] = 'Product name is required';
        }
        
        if (empty($price) || !is_numeric($price) || $price <= 0) {
            $errors['price'] = 'Valid price is required';
        }
        
        if (empty($category_id)) {
            $errors['category_id'] = 'Category is required';
        }
        
        // Handle image upload
        $imageName = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
            $fileType = $_FILES['image']['type'];
            
            if (in_array($fileType, $allowedTypes)) {
                $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $imageName = uniqid() . '.' . $extension;
                $uploadPath = '../assets/images/products/' . $imageName;
                
                // Create directory if it doesn't exist
                if (!is_dir('../assets/images/products/')) {
                    mkdir('../assets/images/products/', 0777, true);
                }
                
                if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                    $errors['image'] = 'Failed to upload image';
                }
            } else {
                $errors['image'] = 'Invalid image type. Only JPG, PNG, and WEBP are allowed';
            }
        }
        
        // If no errors, add product
        if (empty($errors)) {
            $productData = [
                'name' => $name,
                'description' => $description,
                'price' => $price,
                'category_id' => $category_id,
                'image' => $imageName,
                'stock' => $stock,
                'unit' => $unit,
                'is_featured' => $is_featured,
                'is_active' => $is_active
            ];
            
            if ($product->addProduct($productData)) {
                $success = 'Product added successfully!';
                // Clear form data
                $_POST = [];
            } else {
                $errors[] = 'Failed to add product. Please try again.';
            }
        }
    }
}

$pageTitle = 'Add Product - Admin Dashboard';
require_once '../includes/header.php';
?>

<div class="container-fluid mt-4">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 d-md-block bg-light sidebar">
            <div class="sidebar-sticky pt-3">
                <h6 class="sidebar-heading px-3 mt-4 mb-1 text-muted">
                    <span>Admin Panel</span>
                </h6>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="admin.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="add-product.php">
                            <i class="fas fa-plus"></i> Add Product
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">
                            <i class="fas fa-home"></i> View Site
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="../logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main content -->
        <main role="main" class="col-md-10 ml-sm-auto px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Add New Product</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="admin.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($success); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors) && is_array($errors) && !isset($errors['name'])): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo implode('<br>', $errors); ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="csrf_token" value="<?php echo $user->getCSRFToken(); ?>">
                                
                                <div class="mb-3">
                                    <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" 
                                           id="name" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
                                    <?php if (isset($errors['name'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['name']; ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="price" class="form-label">Price (₹) <span class="text-danger">*</span></label>
                                            <input type="number" step="0.01" class="form-control <?php echo isset($errors['price']) ? 'is-invalid' : ''; ?>" 
                                                   id="price" name="price" value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>" required>
                                            <?php if (isset($errors['price'])): ?>
                                                <div class="invalid-feedback"><?php echo $errors['price']; ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                            <select class="form-select <?php echo isset($errors['category_id']) ? 'is-invalid' : ''; ?>" 
                                                    id="category_id" name="category_id" required>
                                                <option value="">Select Category</option>
                                                <?php foreach ($categories as $cat): ?>
                                                    <option value="<?php echo $cat['id']; ?>" 
                                                            <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($cat['name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <?php if (isset($errors['category_id'])): ?>
                                                <div class="invalid-feedback"><?php echo $errors['category_id']; ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="stock" class="form-label">Stock Quantity</label>
                                            <input type="number" class="form-control" id="stock" name="stock" 
                                                   value="<?php echo htmlspecialchars($_POST['stock'] ?? '0'); ?>">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="unit" class="form-label">Unit</label>
                                            <select class="form-select" id="unit" name="unit">
                                                <option value="piece" <?php echo (isset($_POST['unit']) && $_POST['unit'] == 'piece') ? 'selected' : ''; ?>>Piece</option>
                                                <option value="kg" <?php echo (isset($_POST['unit']) && $_POST['unit'] == 'kg') ? 'selected' : ''; ?>>Kilogram (kg)</option>
                                                <option value="liter" <?php echo (isset($_POST['unit']) && $_POST['unit'] == 'liter') ? 'selected' : ''; ?>>Liter</option>
                                                <option value="dozen" <?php echo (isset($_POST['unit']) && $_POST['unit'] == 'dozen') ? 'selected' : ''; ?>>Dozen</option>
                                                <option value="pack" <?php echo (isset($_POST['unit']) && $_POST['unit'] == 'pack') ? 'selected' : ''; ?>>Pack</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="image" class="form-label">Product Image</label>
                                    <input type="file" class="form-control <?php echo isset($errors['image']) ? 'is-invalid' : ''; ?>" 
                                           id="image" name="image" accept="image/*">
                                    <?php if (isset($errors['image'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['image']; ?></div>
                                    <?php endif; ?>
                                    <small class="form-text text-muted">Accepted formats: JPG, PNG, WEBP. Max size: 2MB</small>
                                </div>

                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" 
                                           <?php echo (isset($_POST['is_featured'])) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="is_featured">
                                        Featured Product (Display on homepage)
                                    </label>
                                </div>

                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" 
                                           <?php echo (!isset($_POST['is_active']) || isset($_POST['is_active'])) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="is_active">
                                        Active (Visible to customers)
                                    </label>
                                </div>

                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Add Product
                                </button>
                                <a href="admin.php" class="btn btn-secondary">Cancel</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<style>
.sidebar {
    position: fixed;
    top: 76px;
    bottom: 0;
    left: 0;
    z-index: 100;
    padding: 0;
    box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
}

.sidebar-sticky {
    position: relative;
    top: 0;
    height: calc(100vh - 76px);
    padding-top: .5rem;
    overflow-x: hidden;
    overflow-y: auto;
}

.sidebar .nav-link {
    font-weight: 500;
    color: #333;
    padding: 10px 20px;
}

.sidebar .nav-link.active {
    color: #28a745;
    background-color: rgba(40, 167, 69, 0.1);
    border-left: 3px solid #28a745;
}

.sidebar .nav-link:hover {
    color: #28a745;
    background-color: rgba(40, 167, 69, 0.05);
}

main {
    margin-left: 16.666667%;
}
</style>

<?php require_once '../includes/footer.php'; ?>
