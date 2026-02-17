# Email Verification Code Implementation

## Overview
The email verification system has been implemented to send verification codes to users' emails instead of using signed URL links.

## How It Works

### Registration Flow
1. User registers with email and password
2. User is automatically logged in
3. User is redirected to email verification page (`/email-verification`)
4. Verification code is generated and sent to their email automatically via `SendEmailVerificationNotification` listener

### Verification Flow
1. **Step 1 - Enter Email:**
   - User lands on `/email-verification` GET route
   - If no email in session, user sees form to enter their email
   - User submits email, code is generated and sent
   - User is redirected back to same page with email stored in session

2. **Step 2 - Enter Code:**
   - Now user sees form with their email and code input field
   - User enters 6-digit verification code
   - If correct and not expired, email is marked as verified
   - User is redirected to home page with success message

## Database Changes

### users table
Added two new columns:
- `verification_code` (string, nullable) - Stores the 6-digit verification code
- `verification_code_expires_at` (timestamp, nullable) - Stores when the code expires (15 minutes from generation)

Migration: `database/migrations/2026_02_17_120000_add_verification_code_to_users_table.php`

## Files Created/Modified

### New Files Created
1. **Migration:** `database/migrations/2026_02_17_120000_add_verification_code_to_users_table.php`
   - Adds verification_code and verification_code_expires_at columns

2. **Mail Class:** `app/Mail/SendVerificationCodeMail.php`
   - Mailable class to send verification code emails
   - Uses queue for background execution

3. **Controller:** `app/Http/Controllers/Auth/VerificationCodeController.php`
   - `show()` - Display verification form (email entry or code entry)
   - `send()` - Validate email and send verification code
   - `verify()` - Validate verification code and mark email as verified

4. **Email Template:** `resources/views/emails/verification-code.blade.php`
   - HTML email template showing the verification code
   - Includes 15-minute expiration notice
   - Professional styling with security warning

5. **Verification View:** `resources/views/auth/verify-code.blade.php`
   - Shows email input form initially
   - After email submission, shows code input form
   - Option to use different email or resend code

### Files Modified
1. **Models/User.php** - Added methods:
   - `hasVerifiedEmail()` - Check if email is verified
   - `markEmailAsVerified()` - Mark email as verified
   - `sendVerificationCodeNotification()` - Send verification code email
   - Updated casts for new columns

2. **Http/Controllers/Auth/RegisteredUserController.php**
   - Changed redirect after registration from dashboard to verification notice

3. **Listeners/SendEmailVerificationNotification.php**
   - Updated to call `sendVerificationCodeNotification()` instead of `sendEmailVerificationNotification()`

4. **routes/auth.php** - Added routes:
   - `GET /email-verification` - Show verification form
   - `POST /email-verification/send` - Send code to email
   - `POST /email-verification/verify` - Verify code

## Routes

### Public Routes (Guest Only)
```
GET  /email-verification              -> Show verification form
POST /email-verification/send         -> Send code to email
POST /email-verification/verify       -> Verify code
```

**Route Names:**
- `verification.notice` - GET email verification form
- `verification.send` - POST send code
- `verification.verify` - POST verify code

## Mail Configuration

The application uses SMTP with Gmail:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=europebv1.be@gmail.com
MAIL_PASSWORD=lsanobmozwiyqszr
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="no-reply@qx.com"
MAIL_FROM_NAME="QX Support"
```

## Verification Code Details

- **Length:** 6 digits
- **Format:** Zero-padded (e.g., 001234)
- **Expiration:** 15 minutes from generation
- **Scope:** User-specific and case-sensitive

## Security Features

1. **Code Database Storage:**
   - Codes are stored as plain text in database (for demo)
   - For production, consider hashing verification codes

2. **Expiration:**
   - Codes expire after 15 minutes
   - Expired codes show specific error message
   - Users must request new code for expired codes

3. **Rate Limiting:**
   - Can be added to routes using `throttle` middleware
   - Example: `->middleware('throttle:6,1')` for 6 attempts per minute

4. **Email Validation:**
   - Email must exist in users table
   - Only unverified users can enter verification code
   - Already verified emails show success message

5. **Attempt Validation:**
   - Code must be exactly 6 digits
   - Wrong code shows error message without revealing if email/code exists

6. **Queue Processing:**
   - Emails sent via queue (async) for better performance
   - Queue connection configured to database in `.env`

## Testing the System

### Test Registration and Verification
1. Navigate to `/register`
2. Fill in details with user type (Brand or Creator)
3. Account is created and you're logged in
4. Redirected to `/email-verification`
5. Enter email address
6. Check email logs at `storage/logs/` (if using log driver) or actual mailbox
7. Copy verification code from email
8. Return to form and enter code
9. Email is verified and dashboard becomes accessible

### Check Storage Location
- For development with log driver: `storage/logs/laravel.log`
- For SMTP: Check configured email inbox

### View Database
```sql
SELECT id, email, email_verified_at, verification_code, verification_code_expires_at 
FROM users;
```

## Integration with Protected Routes

The `verified` middleware on dashboard and admin routes automatically:
1. Checks if `email_verified_at` is not null
2. Redirects unverified users to `verification.notice` route
3. Preserves the intended redirect after verification

## Features Summary

✅ Send verification code to email
✅ 6-digit code display in professional HTML email
✅ Verify user by code entry
✅ Code expiration (15 minutes)
✅ Resend code functionality
✅ Email validation
✅ Queue-based email sending
✅ Integration with existing auth flow
✅ Database storage of verification data
✅ Clean two-step form UI
✅ Error messages for expired/incorrect codes
✅ Option to use different email

## Future Enhancements

1. **Rate Limiting:** Add throttle middleware to prevent brute force
   ```php
   ->middleware('throttle:5,15') // 5 attempts per 15 minutes
   ```

2. **Code Hashing:** Hash verification codes in database for security
   ```php
   'verification_code' => Hash::make($verificationCode)
   ```

3. **Admin Interface:** Allow admins to resend or manually verify emails

4. **Verification History:** Track verification attempts and timestamps

5. **Alternative Methods:** Support SMS verification codes as alternative

6. **Custom Expiration:** Make code expiration time configurable via `.env`

7. **Email Customization:** Add user name and company info to email template

## Troubleshooting

### Code not arriving in email
- Check `.env` MAIL settings
- If using log driver, check `storage/logs/laravel.log`
- Verify queue is processing: `php artisan queue:work`

### User sees "Email not found" error
- Ensure user registration was successful
- Check users table for the email address

### Code always shows as expired
- Check server time is correct
- Verify `verification_code_expires_at` column exists
- Run: `php artisan migrate`

### User can't access dashboard after verification
- Check `email_verified_at` is set in database
- Verify `verified` middleware is active on dashboard route
- Clear any cached route files: `php artisan route:clear`

## Console Commands

View logs:
```bash
tail -f storage/logs/laravel.log
```

Process queued emails:
```bash
php artisan queue:work
```

Run migrations:
```bash
php artisan migrate
```
