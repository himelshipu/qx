. Get Stripe API Keys
Go to: https://dashboard.stripe.com/apikeys

Copy Publishable Key (starts with pk_test_)
Copy Secret Key (starts with sk_test_)
2. Add to .env

STRIPE_PUBLIC_KEY=pk_test_YOUR_KEY_HERESTRIPE_SECRET_KEY=sk_test_YOUR_KEY_HERE
3. Install Stripe Package
Run in terminal:


composer require stripe/stripe-php