# Quick Start Guide - Authentication Testing

## Start the Application

1. **Ensure migrations are run:**
   ```bash
   php artisan migrate
   ```

2. **Start the development server:**
   ```bash
   php artisan serve --host=0.0.0.0 --port=8000
   ```

3. **Access the application at:**
   ```
   http://localhost:8000
   ```

## Testing User Registration

### Step 1: Register a New User
1. Navigate to: `http://localhost:8000/register`
2. Fill in the form:
   - **Name:** John Doe
   - **Email:** john@example.com
   - **Password:** password123
   - **Confirm Password:** password123
3. Click "Register"
4. You'll be automatically logged in and redirected to the dashboard

### Step 2: Verify Email Notification
1. Check the email verification prompt at `/verify-email`
2. The verification email has been sent (logged in `storage/logs/laravel.log`)
3. To find the verification link, check the log file:
   ```bash
   tail -50 storage/logs/laravel.log | grep verify
   ```
4. Copy the verification link and open it in the browser
5. Email will be marked as verified

## Testing Email Verification

### Option A: Use Artisan Tinker (Manual Verification)
```bash
php artisan tinker
```

Then in tinker:
```php
$user = User::where('email', 'john@example.com')->first();
$user->markEmailAsVerified();
```

### Option B: Resend Verification Email
1. Go to `/verify-email`
2. Click "Resend Email"
3. Check logs for the new verification link

### Option C: Use Signed URL Directly
1. Check `storage/logs/laravel.log` for the URL
2. The URL will look like: `/verify-email/{id}/{hash}?expires=...&signature=...`
3. Click the link to verify

## Testing Password Reset

### Step 1: Request Password Reset
1. Navigate to: `http://localhost:8000/login`
2. Click "Forgot your password?" link
3. Enter email: `john@example.com`
4. Click "Email Password Reset Link"
5. Check logs for reset link:
   ```bash
   tail -50 storage/logs/laravel.log | grep reset
   ```

### Step 2: Reset Password
1. Open the reset link from logs
2. The URL will look like: `/reset-password/{token}`
3. Enter new password and confirm
4. Click "Reset Password"
5. You'll be redirected to login page with success message

### Step 3: Login with New Password
1. Enter email: `john@example.com`
2. Enter new password
3. Click "Log in"
4. You should be logged in successfully

## Testing Dashboard Access

### Protected Routes
The dashboard is protected by two middleware:
- `auth` - User must be logged in
- `verified` - User's email must be verified

### Workflow
1. Register new user → Auto-login → Redirected to dashboard (if email verified)
2. If email not verified → Redirected to `/verify-email`
3. Verify email → Can access dashboard

### Try accessing `/dashboard` without login
- You'll be redirected to `/login`

## Testing Logout

1. Login to the application
2. Look for logout button (typically in profile menu)
3. Click logout
4. You'll be redirected to `/login`
5. Try accessing `/dashboard` → Redirected to login

## Database Inspection

### Check Users Table
```bash
php artisan tinker
```

Then:
```php
// Get all users
User::all();

// Get specific user
User::where('email', 'john@example.com')->first();

// Check if email verified
$user = User::first();
$user->hasVerifiedEmail();
```

### Check Sessions Table
```php
// List active sessions
DB::table('sessions')->get();

// Check user sessions
$user = User::first();
DB::table('sessions')->where('user_id', $user->id)->get();
```

### Check Password Reset Tokens
```php
// View pending reset tokens
DB::table('password_reset_tokens')->get();
```

## Viewing Emails (Development)

Since `MAIL_MAILER=log`, all emails are logged to:
```
storage/logs/laravel.log
```

### View Recent Emails
```bash
tail -100 storage/logs/laravel.log | grep -A 20 "Message-ID"
```

### Search for Specific Email Type
```bash
# Find verification emails
grep -i "verify" storage/logs/laravel.log

# Find password reset emails
grep -i "password.*reset" storage/logs/laravel.log

# Find all sent emails
grep -i "message-id" storage/logs/laravel.log
```

## Common Test Scenarios

### Scenario 1: Complete Registration Flow
1. Register with new email
2. Auto-logged in
3. See verification prompt
4. Verify email via link
5. Access dashboard
6. Logout

### Scenario 2: Password Reset Flow
1. Goto forgot-password
2. Request reset link
3. Click link from logs
4. Reset password
5. Login with new password

### Scenario 3: Update Profile
1. Login with verified email
2. Go to `/profile`
3. Update name or other info
4. If you change email, email verification is reset
5. Must re-verify email before dashboard access

### Scenario 4: Delete Account
1. Login
2. Go to `/profile`
3. Delete account with password confirmation
4. Account and user sessions are deleted

### Scenario 5: Email Verification Loop
1. Register user (not verified)
2. Try accessing `/dashboard` → Redirected to `/verify-email`
3. Resend verification email
4. Click verification link
5. Now can access `/dashboard`

## Testing with Multiple Users

### Create Multiple Test Users
```bash
php artisan tinker
```

```php
// Create multiple users
User::factory(5)->create();

// Create verified users
User::factory(3)->create([
    'email_verified_at' => now()
]);

// Create unverified users
User::factory(3)->create([
    'email_verified_at' => null
]);
```

### Test User Isolation
- Login as user1
- Logout
- Login as user2
- Each user has separate session
- Can't access other user's sessions

## Troubleshooting

### Issue: Can't see verification email link
**Solution:** Check logs:
```bash
tail -100 storage/logs/laravel.log | grep -i verify
```

### Issue: Password reset link expired
**Solution:** Tokens expire after 60 minutes. Request a new link.

### Issue: Email verification link is invalid
**Solution:** 
- If APP_KEY changed, old links are invalid
- Request a new verification email
- Don't change APP_KEY after signup

### Issue: Can't login after password reset
**Solution:**
- Ensure password was saved correctly
- Check password in users table
- Try resetting again

### Issue: Dashboard says "Verify email" but already verified
**Solution:**
- Clear browser cache
- Logout and login again
- Check email_verified_at field in database

## Security Notes

1. **Password Reset Tokens**
   - Expire after 60 minutes
   - One-time use
   - Deleted after use

2. **Email Verification Links**
   - Signed with APP_KEY
   - Contain user ID and hash
   - Can be resent

3. **Session Security**
   - Sessions stored in database
   - CSRF tokens on all forms
   - Can have multiple concurrent sessions

4. **Rate Limiting**
   - Email verification: 6 per minute
   - Password reset: 1 per minute

## Next Steps

1. **Configure Email Service for Production**
   - Update MAIL_MAILER in .env
   - Set SMTP credentials or API keys
   - Update MAIL_FROM_ADDRESS

2. **Customize Views**
   - Edit files in `resources/views/auth/`
   - Customize colors, text, layout

3. **Modify Validation Rules**
   - Edit RegisteredUserController.php
   - Add password complexity requirements
   - Add username validation if needed

4. **Add Additional Features**
   - Two-factor authentication
   - Social login (Google, GitHub, etc.)
   - User roles and permissions
