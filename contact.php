<?php
require_once 'includes/init.php';

$pageTitle = 'Contact Us - FreshMart';
require_once 'includes/header.php';

// Handle form submission
$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Basic validation
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // Here you would typically save to database or send email
        // For now, we'll just show success message
        $success = true;
    }
}
?>

<!-- Contact Hero Section -->
<section class="contact-hero py-5 bg-success text-white">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                <h1 class="display-4 mb-3">Contact Us</h1>
                <p class="lead">We'd love to hear from you. Get in touch with us!</p>
            </div>
        </div>
    </div>
</section>

<!-- Contact Information -->
<section class="py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="fas fa-map-marker-alt fa-3x text-success"></i>
                        </div>
                        <h5 class="card-title">Visit Us</h5>
                        <p class="text-muted">123 Fresh Street<br>Green Valley, GV 12345<br>India</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="fas fa-phone fa-3x text-success"></i>
                        </div>
                        <h5 class="card-title">Call Us</h5>
                        <p class="text-muted">+91 1234567890<br>Mon-Sat: 9AM - 8PM<br>Sunday: 10AM - 6PM</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="fas fa-envelope fa-3x text-success"></i>
                        </div>
                        <h5 class="card-title">Email Us</h5>
                        <p class="text-muted">support@freshmart.com<br>info@freshmart.com<br>We reply within 24 hours</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5">
                        <h3 class="mb-4 text-center">Send Us a Message</h3>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                Thank you for contacting us! We'll get back to you soon.
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <?php echo htmlspecialchars($error); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="name" class="form-label">Your Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="subject" name="subject" 
                                       value="<?php echo htmlspecialchars($_POST['subject'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="message" name="message" rows="5" required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>Send Message
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Section (Optional) -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h3 class="text-center mb-4">Find Us on Map</h3>
                <div class="map-container" style="height: 400px; background: #e9ecef; display: flex; align-items: center; justify-content: center; border-radius: 10px;">
                    <div class="text-center text-muted">
                        <i class="fas fa-map-marked-alt fa-4x mb-3"></i>
                        <p>Map integration can be added here</p>
                        <small>Use Google Maps or any other map service</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-5">
    <div class="container">
        <h3 class="text-center mb-5">Frequently Asked Questions</h3>
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                What are your delivery hours?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                We deliver from 9 AM to 8 PM on weekdays and 10 AM to 6 PM on Sundays. Orders placed before 6 PM are typically delivered the same day.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                What is your return policy?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                We offer a 100% satisfaction guarantee. If you're not happy with any product, contact us within 24 hours of delivery for a full refund or replacement.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                Do you have a minimum order value?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Yes, we have a minimum order value of ₹200. Orders above ₹500 get free delivery!
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                What payment methods do you accept?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                We accept all major credit/debit cards, UPI, net banking, and cash on delivery for orders under ₹2000.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.contact-hero {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.card {
    transition: transform 0.3s;
}

.card:hover {
    transform: translateY(-5px);
}

.accordion-button:not(.collapsed) {
    background-color: #28a745;
    color: white;
}

.accordion-button:focus {
    box-shadow: none;
    border-color: #28a745;
}
</style>

<?php require_once 'includes/footer.php'; ?>
