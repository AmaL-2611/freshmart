# Razorpay Payment Gateway Integration

## Setup Instructions

### 1. Create Razorpay Account

1. Go to [https://razorpay.com/](https://razorpay.com/)
2. Click on "Sign Up" and create an account
3. Complete the verification process

### 2. Get API Keys

1. Login to Razorpay Dashboard
2. Go to **Settings** → **API Keys**
3. Click on **Generate Test Key** (for testing)
4. Copy both:
   - **Key ID** (starts with `rzp_test_`)
   - **Key Secret**

### 3. Configure Your Application

1. Open `config/razorpay.php`
2. Replace the placeholder values:
   ```php
   define('RAZORPAY_KEY_ID', 'rzp_test_YOUR_ACTUAL_KEY_ID');
   define('RAZORPAY_KEY_SECRET', 'YOUR_ACTUAL_KEY_SECRET');
   ```

### 4. Run Database Setup

1. Go to: `http://localhost/anki/setup-database.php`
2. This will create the `payment_transactions` table

### 5. Test the Payment

1. Add products to cart
2. Go to checkout
3. Select "Pay Online (Razorpay)"
4. Click "Place Order"
5. Razorpay popup will open

#### Test Card Details (for testing):

**Card Number:** 4111 1111 1111 1111  
**CVV:** Any 3 digits  
**Expiry:** Any future date  
**Name:** Any name

**UPI ID (for testing):** success@razorpay

### 6. Payment Flow

1. User selects Razorpay payment method
2. Clicks "Place Order"
3. Razorpay payment modal opens
4. User completes payment
5. Payment success → Order is created
6. Stock is reduced automatically
7. User redirected to order success page

### 7. Going Live (Production)

When ready for production:

1. Complete KYC verification on Razorpay
2. Get **Live API Keys** from Razorpay Dashboard
3. Update `config/razorpay.php` with live keys:
   ```php
   define('RAZORPAY_KEY_ID', 'rzp_live_YOUR_LIVE_KEY');
   define('RAZORPAY_KEY_SECRET', 'YOUR_LIVE_SECRET');
   ```

### 8. Features Implemented

✅ Razorpay payment integration  
✅ Cash on Delivery (COD) option  
✅ Payment verification  
✅ Order creation after successful payment  
✅ Stock management  
✅ Payment transaction logging  
✅ User-friendly payment modal  
✅ Error handling  

### 9. Security Notes

- Never commit your API keys to version control
- Use environment variables for production
- Keep `RAZORPAY_KEY_SECRET` secure
- Enable webhook signature verification for production

### 10. Troubleshooting

**Payment not working?**
- Check if API keys are correct
- Ensure Razorpay script is loaded
- Check browser console for errors
- Verify database tables are created

**Order not created after payment?**
- Check `process-razorpay.php` for errors
- Verify cart has items
- Check user is logged in

### Support

For Razorpay documentation: [https://razorpay.com/docs/](https://razorpay.com/docs/)

For integration issues, check:
- Browser console (F12)
- PHP error logs
- Network tab in developer tools
