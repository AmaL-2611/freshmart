<?php
/**
 * Razorpay Configuration
 * 
 * To get your Razorpay API keys:
 * 1. Sign up at https://razorpay.com/
 * 2. Go to Dashboard > Settings > API Keys
 * 3. Generate Test/Live keys
 * 4. Replace the values below
 */

// Test Mode Keys (for development)
define('RAZORPAY_KEY_ID', 'rzp_test_RVQbH1AONwR4nQ');
define('RAZORPAY_KEY_SECRET', 'qRlyfDnrlYABCDPgKA7sGF97');

// Live Mode Keys (for production - uncomment when going live)
// define('RAZORPAY_KEY_ID', 'rzp_live_YOUR_LIVE_KEY_ID');
// define('RAZORPAY_KEY_SECRET', 'YOUR_LIVE_KEY_SECRET');

// Razorpay Settings
define('RAZORPAY_CURRENCY', 'INR');
define('RAZORPAY_COMPANY_NAME', 'FreshMart');
?>
