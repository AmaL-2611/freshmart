<?php
require_once 'includes/init.php';

// Initialize User class
$user = new User($db);

// Redirect if already logged in
if ($user->isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$errors = [];
$formData = [
    'name' => '',
    'email' => '',
    'phone' => '',
    'address' => ''
];

// Process registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !$user->validateCSRFToken($_POST['csrf_token'])) {
        $errors[] = 'Invalid request. Please try again.';
    } else {
        // Sanitize and validate input
        $formData['name'] = trim($_POST['name'] ?? '');
        $formData['email'] = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $formData['phone'] = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validation
        if (empty($formData['name'])) {
            $errors['name'] = 'Name is required';
        } elseif (strlen($formData['name']) < 2) {
            $errors['name'] = 'Name must be at least 2 characters long';
        }
        
        if (empty($formData['email'])) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address';
        } else {
            // Check if email already exists
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$formData['email']]);
            if ($stmt->fetch()) {
                $errors['email'] = 'This email is already registered';
            }
        }
        
        if (!empty($formData['phone']) && !preg_match('/^[0-9\-\+\(\)\s]{10,20}$/', $formData['phone'])) {
            $errors['phone'] = 'Please enter a valid phone number';
        }
        
        if (strlen($password) < 8) {
            $errors['password'] = 'Password must be at least 8 characters long';
        } elseif (!preg_match('/[A-Z]/', $password)) {
            $errors['password'] = 'Password must contain at least one uppercase letter';
        } elseif (!preg_match('/[a-z]/', $password)) {
            $errors['password'] = 'Password must contain at least one lowercase letter';
        } elseif (!preg_match('/[0-9]/', $password)) {
            $errors['password'] = 'Password must contain at least one number';
        } elseif ($password !== $confirmPassword) {
            $errors['confirm_password'] = 'Passwords do not match';
        }
        
        // If no errors, proceed with registration
        if (empty($errors)) {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user into database
            try {
                $stmt = $db->prepare("
                    INSERT INTO users (name, email, password, phone) 
                    VALUES (?, ?, ?, ?)
                ");
                
                $stmt->execute([
                    $formData['name'],
                    $formData['email'],
                    $hashedPassword,
                    !empty($formData['phone']) ? $formData['phone'] : null
                ]);
                
                // Auto-login the user after registration
                if ($user->login($formData['email'], $password)) {
                    // Redirect to success page or dashboard
                    header('Location: registration-success.php');
                    exit();
                } else {
                    $errors[] = 'Registration successful but login failed. Please try logging in.';
                }
                
            } catch (PDOException $e) {
                $errors[] = 'Registration failed. Please try again later.';
                // Log the error for debugging
                error_log('Registration error: ' . $e->getMessage());
            }
        }
    }
}

$pageTitle = 'Create Account - FreshMart';
require_once 'includes/header.php';
?>

<!-- Registration Section -->
<section class="py-5 my-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold">Create an Account</h2>
                            <p class="text-muted">Join FreshMart to start shopping</p>
                        </div>
                        
                        <?php if (!empty($errors) && is_array($errors) && !isset($errors['name']) && !isset($errors['email']) && !isset($errors['password']) && !isset($errors['confirm_password'])): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo implode('<br>', $errors); ?>
                            </div>
                        <?php endif; ?>
                        
                        <form id="registerForm" method="POST" action="register.php" novalidate>
                            <input type="hidden" name="csrf_token" value="<?php echo $user->getCSRFToken(); ?>">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                            <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" 
                                                   id="name" name="name" 
                                                   value="<?php echo htmlspecialchars($formData['name']); ?>" 
                                                   required>
                                        </div>
                                        <?php if (isset($errors['name'])): ?>
                                            <div class="invalid-feedback d-block"><?php echo $errors['name']; ?></div>
                                        <?php else: ?>
                                            <div class="form-text">Your full name as it appears on your ID</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                            <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                                                   id="email" name="email" 
                                                   value="<?php echo htmlspecialchars($formData['email']); ?>" 
                                                   required>
                                        </div>
                                        <?php if (isset($errors['email'])): ?>
                                            <div class="invalid-feedback d-block"><?php echo $errors['email']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                            <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" 
                                                   id="password" name="password" 
                                                   required
                                                   minlength="8">
                                            <button class="btn btn-outline-secondary toggle-password" type="button">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        <?php if (isset($errors['password'])): ?>
                                            <div class="invalid-feedback d-block"><?php echo $errors['password']; ?></div>
                                        <?php else: ?>
                                            <div class="form-text">At least 8 characters, with uppercase, lowercase, and number</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="confirm_password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                            <input type="password" class="form-control <?php echo isset($errors['confirm_password']) ? 'is-invalid' : ''; ?>" 
                                                   id="confirm_password" name="confirm_password" 
                                                   required
                                                   minlength="8">
                                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="confirm_password">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        <?php if (isset($errors['confirm_password'])): ?>
                                            <div class="invalid-feedback d-block"><?php echo $errors['confirm_password']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                            <input type="tel" class="form-control <?php echo isset($errors['phone']) ? 'is-invalid' : ''; ?>" 
                                                   id="phone" name="phone" 
                                                   value="<?php echo htmlspecialchars($formData['phone']); ?>">
                                        </div>
                                        <?php if (isset($errors['phone'])): ?>
                                            <div class="invalid-feedback d-block"><?php echo $errors['phone']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
                                <i class="fas fa-user-plus me-2"></i> Create Account
                            </button>
                            
                            <div class="text-center mt-4">
                                <p class="mb-0">Already have an account? 
                                    <a href="login.php" class="text-primary fw-bold">Sign In</a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Custom JavaScript for form validation -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registerForm');
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    
    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('data-target') || 'password';
            const input = document.getElementById(targetId);
            if (input) {
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            }
        });
    });
    
    // Add focus and blur events for validation
    [nameInput, emailInput, phoneInput, passwordInput, confirmPasswordInput].forEach(input => {
        if (input) {
            // Remove error on focus
            input.addEventListener('focus', function() {
                this.classList.remove('is-invalid');
                // Find and hide the error message
                const parent = this.closest('.input-group') || this.parentElement;
                const errorMsg = parent.parentElement.querySelector('.invalid-feedback');
                if (errorMsg && !errorMsg.classList.contains('d-block')) {
                    errorMsg.style.display = 'none';
                }
            });
            
            // Show validation on blur
            input.addEventListener('blur', function() {
                if (this.value.trim() !== '') {
                    validateField(this);
                }
            });
        }
    });
    
    // Phone number formatting
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
            e.target.value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
        });
    }
    
    // Field validation function
    function validateField(field) {
        const value = field.value.trim();
        let isValid = true;
        let errorMessage = '';
        
        switch(field.id) {
            case 'name':
                if (!value) {
                    errorMessage = 'Name is required';
                    isValid = false;
                } else if (value.length < 2) {
                    errorMessage = 'Name must be at least 2 characters long';
                    isValid = false;
                }
                break;
                
            case 'email':
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!value) {
                    errorMessage = 'Email is required';
                    isValid = false;
                } else if (!emailRegex.test(value)) {
                    errorMessage = 'Please enter a valid email address';
                    isValid = false;
                }
                break;
                
            case 'phone':
                if (value && !/^[0-9\-\+\(\)\s]{10,20}$/.test(value)) {
                    errorMessage = 'Please enter a valid phone number';
                    isValid = false;
                }
                break;
                
            case 'password':
                if (!value) {
                    errorMessage = 'Password is required';
                    isValid = false;
                } else if (value.length < 8) {
                    errorMessage = 'Password must be at least 8 characters long';
                    isValid = false;
                } else if (!/[A-Z]/.test(value)) {
                    errorMessage = 'Must contain at least one uppercase letter';
                    isValid = false;
                } else if (!/[a-z]/.test(value)) {
                    errorMessage = 'Must contain at least one lowercase letter';
                    isValid = false;
                } else if (!/[0-9]/.test(value)) {
                    errorMessage = 'Must contain at least one number';
                    isValid = false;
                }
                break;
                
            case 'confirm_password':
                const password = document.getElementById('password').value;
                if (!value) {
                    errorMessage = 'Please confirm your password';
                    isValid = false;
                } else if (value !== password) {
                    errorMessage = 'Passwords do not match';
                    isValid = false;
                }
                break;
        }
        
        if (!isValid) {
            showError(field, errorMessage);
        } else {
            clearError(field);
        }
        
        return isValid;
    }
    
    // Helper function to show error
    function showError(input, message) {
        input.classList.add('is-invalid');
        const parent = input.closest('.input-group') || input.parentElement;
        let errorDiv = parent.parentElement.querySelector('.invalid-feedback');
        
        if (!errorDiv || errorDiv.classList.contains('d-block')) {
            // Create new error div if doesn't exist or is server-side error
            errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            errorDiv.style.display = 'block';
            parent.parentElement.appendChild(errorDiv);
        }
        
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
    }
    
    // Helper function to clear error
    function clearError(input) {
        input.classList.remove('is-invalid');
        const parent = input.closest('.input-group') || input.parentElement;
        const errorDiv = parent.parentElement.querySelector('.invalid-feedback');
        if (errorDiv && !errorDiv.classList.contains('d-block')) {
            errorDiv.style.display = 'none';
        }
    }
    
    // Form submission
    if (form) {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Validate all required fields
            [nameInput, emailInput, passwordInput, confirmPasswordInput].forEach(field => {
                if (field && !validateField(field)) {
                    isValid = false;
                }
            });
            
            // Validate phone if filled
            if (phoneInput && phoneInput.value.trim() !== '' && !validateField(phoneInput)) {
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
                // Scroll to first error
                const firstError = document.querySelector('.is-invalid');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstError.focus();
                }
            } else {
                // Show loading state
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Creating Account...';
                }
            }
        });
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
