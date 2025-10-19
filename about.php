<?php
require_once 'includes/init.php';

$pageTitle = 'About Us - FreshMart';
require_once 'includes/header.php';
?>

<!-- About Hero Section -->
<section class="about-hero py-5 bg-success text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="display-4 mb-4">About FreshMart</h1>
                <p class="lead">Your trusted partner for fresh groceries delivered right to your doorstep</p>
            </div>
            <div class="col-md-6">
                <img src="assets/images/about-hero.jpg" alt="FreshMart" class="img-fluid rounded" 
                     onerror="this.src='https://via.placeholder.com/600x400?text=FreshMart'">
            </div>
        </div>
    </div>
</section>

<!-- Our Story -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center mb-5">
                <h2 class="mb-3">Our Story</h2>
                <p class="lead text-muted">Bringing freshness to your table since 2024</p>
            </div>
        </div>
        <div class="row align-items-center">
            <div class="col-md-6">
                <img src="assets/images/our-story.jpg" alt="Our Story" class="img-fluid rounded mb-4"
                     onerror="this.src='https://via.placeholder.com/500x350?text=Our+Story'">
            </div>
            <div class="col-md-6">
                <h3 class="mb-3">Fresh, Quality, Delivered</h3>
                <p>FreshMart was founded with a simple mission: to make fresh, quality groceries accessible to everyone. We believe that everyone deserves access to fresh produce, dairy products, and pantry essentials without the hassle of visiting multiple stores.</p>
                <p>Our team works directly with local farmers and trusted suppliers to ensure that every product meets our high standards of quality and freshness. From farm to your doorstep, we maintain the cold chain and handle products with care.</p>
                <p>Today, we serve thousands of happy customers across the city, delivering fresh groceries within hours of your order.</p>
            </div>
        </div>
    </div>
</section>

<!-- Our Values -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-md-12">
                <h2 class="mb-3">Our Values</h2>
                <p class="lead text-muted">What drives us every day</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="fas fa-leaf fa-3x text-success"></i>
                        </div>
                        <h4>Freshness First</h4>
                        <p class="text-muted">We source directly from farms and ensure products reach you at peak freshness.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="fas fa-shield-alt fa-3x text-success"></i>
                        </div>
                        <h4>Quality Assured</h4>
                        <p class="text-muted">Every product is carefully inspected to meet our strict quality standards.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="fas fa-truck fa-3x text-success"></i>
                        </div>
                        <h4>Fast Delivery</h4>
                        <p class="text-muted">Get your groceries delivered within 2 hours of placing your order.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="fas fa-hand-holding-heart fa-3x text-success"></i>
                        </div>
                        <h4>Customer Care</h4>
                        <p class="text-muted">Our dedicated support team is always ready to help you with any queries.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="fas fa-tags fa-3x text-success"></i>
                        </div>
                        <h4>Best Prices</h4>
                        <p class="text-muted">Competitive pricing without compromising on quality or freshness.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="fas fa-recycle fa-3x text-success"></i>
                        </div>
                        <h4>Sustainability</h4>
                        <p class="text-muted">We're committed to eco-friendly packaging and sustainable practices.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistics -->
<section class="py-5">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 mb-4">
                <div class="stat-box">
                    <h2 class="display-4 text-success mb-2">5000+</h2>
                    <p class="text-muted">Happy Customers</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stat-box">
                    <h2 class="display-4 text-success mb-2">500+</h2>
                    <p class="text-muted">Products</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stat-box">
                    <h2 class="display-4 text-success mb-2">50+</h2>
                    <p class="text-muted">Local Farmers</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stat-box">
                    <h2 class="display-4 text-success mb-2">2hrs</h2>
                    <p class="text-muted">Delivery Time</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-md-12">
                <h2 class="mb-3">Meet Our Team</h2>
                <p class="lead text-muted">The people behind FreshMart</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm">
                    <img src="https://via.placeholder.com/300x300?text=Team+Member" class="card-img-top" alt="Team Member">
                    <div class="card-body text-center">
                        <h5 class="card-title">John Doe</h5>
                        <p class="text-muted">Founder & CEO</p>
                        <div class="social-links">
                            <a href="#" class="text-success me-2"><i class="fab fa-linkedin fa-lg"></i></a>
                            <a href="#" class="text-success me-2"><i class="fab fa-twitter fa-lg"></i></a>
                            <a href="#" class="text-success"><i class="fab fa-facebook fa-lg"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm">
                    <img src="https://via.placeholder.com/300x300?text=Team+Member" class="card-img-top" alt="Team Member">
                    <div class="card-body text-center">
                        <h5 class="card-title">Jane Smith</h5>
                        <p class="text-muted">Operations Manager</p>
                        <div class="social-links">
                            <a href="#" class="text-success me-2"><i class="fab fa-linkedin fa-lg"></i></a>
                            <a href="#" class="text-success me-2"><i class="fab fa-twitter fa-lg"></i></a>
                            <a href="#" class="text-success"><i class="fab fa-facebook fa-lg"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm">
                    <img src="https://via.placeholder.com/300x300?text=Team+Member" class="card-img-top" alt="Team Member">
                    <div class="card-body text-center">
                        <h5 class="card-title">Mike Johnson</h5>
                        <p class="text-muted">Head of Logistics</p>
                        <div class="social-links">
                            <a href="#" class="text-success me-2"><i class="fab fa-linkedin fa-lg"></i></a>
                            <a href="#" class="text-success me-2"><i class="fab fa-twitter fa-lg"></i></a>
                            <a href="#" class="text-success"><i class="fab fa-facebook fa-lg"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-5 bg-success text-white">
    <div class="container text-center">
        <h2 class="mb-4">Ready to Experience Fresh?</h2>
        <p class="lead mb-4">Join thousands of satisfied customers who trust FreshMart for their daily grocery needs</p>
        <a href="index.php" class="btn btn-light btn-lg">Start Shopping</a>
    </div>
</section>

<style>
.about-hero {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.stat-box {
    padding: 20px;
}

.card {
    transition: transform 0.3s;
}

.card:hover {
    transform: translateY(-5px);
}

.social-links a {
    transition: transform 0.3s;
    display: inline-block;
}

.social-links a:hover {
    transform: scale(1.2);
}
</style>

<?php require_once 'includes/footer.php'; ?>
