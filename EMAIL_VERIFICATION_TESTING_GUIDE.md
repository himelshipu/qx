# Email Verification Code - Quick Testing Guide

## Prerequisites
- Application running locally or on server
- Database migrated with verification code columns
- Mail configured (currently using Gmail SMTP)
- Queue processing running (`php artisan queue:work`)

## Test Flow

### Step 1: Register a New User

1. Navigate to: `http://qx.local/register`
2. Select user type: **Brand** or **Creator**
3. Fill in the form:
   ```
   Name: John Doe
   Email: john@example.com (use a real email you have access to)
   Password: SecurePassword123!
   Confirm Password: SecurePassword123!
   Brand Name (for Brand users): My Brand
   ```
4. Click **Register**
5. You'll be logged in and redirected to email verification page

### Step 2: Send Verification Code

1. You should now be on: `http://qx.local/email-verification`
2. The page shows a form asking for your email address
3. Enter your email: `john@example.com`
4. Click **Send Verification Code**
5. You'll see message: "Verification code sent to your email. Valid for 15 minutes."

### Step 3: Check Your Email

**Method 1: SMTP (Real Email)**
- Check your actual email inbox (Gmail in this case)
- You should receive an email from: no-reply@qx.com
- Subject: "Email Verification"
- Copy the 6-digit code from the email

**Method 2: Log Driver (Development)**
- Check file: `/var/www/qx/storage/logs/laravel.log`
- Search for "Email Verification" or the 6-digit code
- Copy the code from the log

**Method 3: Database Query**
```bash
cd /var/www/qx
php artisan tinker
>>> $user = App\Models\User::where('email', 'john@example.com')->first();
>>> $user->verification_code;
```

### Step 4: Enter Verification Code

1. Return to email verification page
2. The page now shows:
   - Your email address
   - Form to enter the 6-digit verification code
3. Enter the code: `123456` (example)
4. Click **Verify**

### Step 5: Verify Success

On successful verification:
- You'll see: "Your email has been verified successfully!"
- You'll be redirected to home page
- Your `email_verified_at` timestamp will be set in the database
- `verification_code` and `verification_code_expires_at` will be cleared

## Testing Different Scenarios

### Test Case 1: Incorrect Code
1. After sending code, enter a wrong code (e.g., `999999`)
2. Click Verify
3. Expected error: "The verification code is incorrect."

### Test Case 2: Expired Code
1. Send verification code
2. Wait 15 minutes (or modify code expiration in controller)
3. Enter the correct code
4. Expected error: "The verification code has expired. Please request a new one."

### Test Case 3: Resend Code
1. After sending code once, click "Send again"
2. New code is generated and sent
3. Use new code to verify

### Test Case 4: Use Different Email
1. During verification, click "Use a different email?"
2. It returns to form asking for email
3. Enter a different registered user's email
4. This should work (or fail with "user not found" if email doesn't exist)

### Test Case 5: Access Dashboard Before Verification
1. Register new user
2. Navigate to: `http://qx.local/dashboard` (without verifying)
3. You should be redirected back to: `/email-verification`
4. After verification, dashboard becomes accessible

### Test Case 6: Already Verified User
1. Verify a user's email
2. Logout
3. Go to `/email-verification`
4. Enter the already-verified email
5. Expected: May show "Email already verified" or allow regenerating

## Database Checks

### Check User Verification Status
```bash
php artisan tinker
>>> $user = App\Models\User::where('email', 'john@example.com')->first();
>>> $user->email_verified_at;        // Should be null before verification
>>> $user->verification_code;        // Should have 6-digit code while waiting
>>> $user->verification_code_expires_at; // Should have timestamp
```

### After Verification
```bash
>>> $user->refresh();
>>> $user->email_verified_at;        // Should now be a timestamp
>>> $user->verification_code;        // Should be null
>>> $user->verification_code_expires_at; // Should be null
```

### List All Users with Verification Status
```bash
php artisan tinker
>>> App\Models\User::select('id', 'email', 'email_verified_at', 'verification_code')->get();
```

## Common Issues & Solutions

### Issue: "Email not found" error
**Solution:**
- Make sure the email is registered in the system
- Check database: `SELECT * FROM users WHERE email = 'john@example.com';`

### Issue: Code not received in email
**Solution:**
- Check `.env` MAIL settings are correct
- If using log driver, check `storage/logs/laravel.log`
- Make sure queue is running: `php artisan queue:work`
- Check email isn't going to spam folder

### Issue: Code shows as always expired
**Solution:**
- Verify `verification_code_expires_at` timestamp is in the future
- Check server time is correct: `date`
- Run migration if column doesn't exist: `php artisan migrate`

### Issue: Can't access dashboard after verification
**Solution:**
- Refresh browser and login again
- Check `email_verified_at` is set: `php artisan tinker` → `User::find(1)->email_verified_at`
- Clear route cache: `php artisan route:clear`

## Email Content

The verification code email includes:
- Professional HTML template
- 6-digit code displayed prominently
- User's name
- Expiration notice (15 minutes)
- Security warning not to share code
- Non-reply notice

## Routes for Testing

```
GET  /register                   - User registration form
POST /register                   - Create new user account
GET  /login                      - User login form
POST /login                      - Authenticate user
GET  /email-verification         - Show email verification form/code entry
POST /email-verification/send    - Send verification code to email
POST /email-verification/verify  - Verify the code entered
GET  /dashboard                  - Protected route (requires verified email)
```

## Performance Notes

- Verification codes are generated in real-time (no delays)
- Emails are queued for background sending
- Code validation is immediate (with expiry check)
- Database operations are minimal and indexed

## Browser Console Testing

No special JavaScript testing needed. The form handles all validation server-side with proper error messages displayed in the UI.

## Troubleshooting Commands

```bash
# Check if queue worker is running
ps aux | grep "queue:work"

# Start queue worker (if not running)
php artisan queue:work

# Clear all caches
php artisan cache:clear

# Regenerate route cache
php artisan route:cache

# Check Laravel config
php artisan config:show mail

# Test email sending
php artisan tinker
>>> Mail::raw('Test', function($message) { $message->to('test@example.com'); });
```

## Success Checklist

- [ ] User can register
- [ ] After registration, redirected to verification page
- [ ] Can enter email and receive code
- [ ] Code appears in email/logs
- [ ] Can verify with correct code
- [ ] Error for incorrect code
- [ ] Error for expired code  
- [ ] Can resend code
- [ ] After verification, can access dashboard
- [ ] Unverified users redirected to verify page when accessing dashboard

## Next Steps

Once testing is complete:
1. Consider adding rate limiting to prevent brute force (`->middleware('throttle:5,15')`)
2. Hash verification codes in production for security
3. Add admin panel to manage user verification
4. Customize email template with logo/branding
5. Add SMS verification as alternative method
