# Laravel Breeze Authentication Setup Documentation

## Overview
This application is configured with **Laravel Breeze** for complete authentication functionality including user registration, email verification, password reset, and session management.

## Features Implemented

### 1. User Registration
- **Route:** `/register`
- **File:** [app/Http/Controllers/Auth/RegisteredUserController.php](app/Http/Controllers/Auth/RegisteredUserController.php)
- **Functionality:**
  - Validates user input (name, email, password)
  - Creates new user account
  - Hashes password using bcrypt
  - Fires `Registered` event to trigger email verification notification
  - Auto-logs in the user after registration

### 2. Email Verification
- **Routes:**
  - `/verify-email` - Shows email verification prompt
  - `/verify-email/{id}/{hash}` - Verifies email via signed URL
  - `/email/verification-notification` - Resends verification email
  
- **Files:**
  - [app/Http/Controllers/Auth/VerifyEmailController.php](app/Http/Controllers/Auth/VerifyEmailController.php)
  - [app/Http/Controllers/Auth/EmailVerificationPromptController.php](app/Http/Controllers/Auth/EmailVerificationPromptController.php)
  - [app/Http/Controllers/Auth/EmailVerificationNotificationController.php](app/Http/Controllers/Auth/EmailVerificationNotificationController.php)
  - [app/Listeners/SendEmailVerificationNotification.php](app/Listeners/SendEmailVerificationNotification.php)

- **Functionality:**
  - User model implements `MustVerifyEmail` interface
  - When user registers, email verification notification is automatically sent
  - Verification link is signed and time-limited
  - Dashboard access requires verified email (via `verified` middleware)
  - Users can resend verification email if needed

### 3. Password Reset
- **Routes:**
  - `/forgot-password` - Show password reset request form
  - `/reset-password/{token}` - Show password reset confirmation form
  
- **Files:**
  - [app/Http/Controllers/Auth/PasswordResetLinkController.php](app/Http/Controllers/Auth/PasswordResetLinkController.php)
  - [app/Http/Controllers/Auth/NewPasswordController.php](app/Http/Controllers/Auth/NewPasswordController.php)

- **Functionality:**
  - User can request password reset by email
  - Reset token is generated and stored in `password_reset_tokens` table
  - Token expires after 60 minutes
  - User must reset password within expiration time
  - Password is hashed before saving

### 4. User Login/Logout
- **Routes:**
  - `/login` - Show login form
  - `/logout` - Logout user

- **File:** [app/Http/Controllers/Auth/AuthenticatedSessionController.php](app/Http/Controllers/Auth/AuthenticatedSessionController.php)

- **Functionality:**
  - Session-based authentication
  - Remembers user via session
  - Throttled login attempts

### 5. Password Management
- **Route:** `/password` (Update password while authenticated)
- **File:** [app/Http/Controllers/Auth/PasswordController.php](app/Http/Controllers/Auth/PasswordController.php)

## Database Setup

### Migrations Created
- **users table** - Stores user data with `email_verified_at` field
- **password_reset_tokens table** - Stores password reset tokens
- **sessions table** - Stores user sessions (since SESSION_DRIVER=database)

### User Model
- **File:** [app/Models/User.php](app/Models/User.php)
- Implements `MustVerifyEmail` interface for email verification
- Casts `email_verified_at` to datetime
- Password is hashed using bcrypt

## Mail Configuration

### Current Setup
- **MAIL_MAILER:** `log` (Development)
- **MAIL_FROM_ADDRESS:** `noreply@qx.local`
- **MAIL_FROM_NAME:** `QX Application`

### For Production
Update .env with your mail service credentials:

```env
# Using SMTP
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_SCHEME=tls
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password

# Or use other services
# MAIL_MAILER=mailgun
# MAIL_MAILER=postmark
# MAIL_MAILER=ses
# MAIL_MAILER=resend
```

## Event Listeners

### SendEmailVerificationNotification
- **File:** [app/Listeners/SendEmailVerificationNotification.php](app/Listeners/SendEmailVerificationNotification.php)
- **Trigger:** `Illuminate\Auth\Events\Registered` event
- **Action:** Sends email verification notification to newly registered users

### EventServiceProvider
- **File:** [app/Providers/EventServiceProvider.php](app/Providers/EventServiceProvider.php)
- Maps events to listeners
- Registered in [bootstrap/providers.php](bootstrap/providers.php)

## Authentication Routes

All routes are defined in [routes/auth.php](routes/auth.php):

### Public Routes (Guest Only)
- `GET /register` - Registration form
- `POST /register` - Store registration
- `GET /login` - Login form
- `POST /login` - Authenticate user
- `GET /forgot-password` - Password reset request form
- `POST /forgot-password` - Send password reset link
- `GET /reset-password/{token}` - Password reset confirmation form
- `POST /reset-password` - Update password

### Authenticated Routes
- `GET /verify-email` - Email verification prompt
- `GET /verify-email/{id}/{hash}` - Verify email via signed link
- `POST /email/verification-notification` - Resend verification email
- `GET /confirm-password` - Confirm password before sensitive action
- `POST /confirm-password` - Verify password
- `PUT /password` - Update authenticated user password
- `POST /logout` - Logout user

## Views

All Blade templates are located in [resources/views/auth/](resources/views/auth/):
- `login.blade.php`
- `register.blade.php`
- `forgot-password.blade.php`
- `reset-password.blade.php`
- `verify-email.blade.php`
- `confirm-password.blade.php`

## Authentication Middleware

The application uses Laravel's built-in middleware:
- `auth` - Requires user to be authenticated
- `guest` - Requires user to NOT be authenticated
- `verified` - Requires user's email to be verified

Example usage:
```php
// Protect route with authentication and email verification
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
```

## Testing Authentication

### Test User Registration
1. Navigate to `/register`
2. Fill in name, email, password
3. User is created and automatically logged in
4. Verification email is sent (logged in storage/logs/ in development)

### Test Email Verification
1. After registration, user should see `/verify-email` prompt
2. Click "Resend Verification Email" to resend link
3. Check logs for verification link
4. Click link in email to verify email address

### Test Password Reset
1. Navigate to `/login`, click "Forgot password"
2. Enter email address
3. Check logs for reset link
4. Click link to reset password form
5. Enter new password and confirm
6. Login with new password

### Test Session Management
1. Login with valid credentials
2. Session is stored in sessions table
3. User stays logged in across requests
4. Logout clears session

## Important Notes

1. **Email Verification Required:** Dashboard requires `verify` middleware, so users must verify email before accessing protected routes.

2. **Password Reset Tokens:** Expire after 60 minutes (configurable in [config/auth.php](config/auth.php))

3. **Session Storage:** Uses database (SESSION_DRIVER=database), ensure sessions table is migrated

4. **Mail Configuration:** 
   - Development: Uses `log` driver to write emails to logs
   - Check `storage/logs/` for email content in development

5. **CSRF Protection:** All forms automatically include CSRF token via Breeze

6. **Throttling:** 
   - Email verification limited to 6 per minute
   - Password reset limited to 1 per minute

## Troubleshooting

### Emails not sending
- Check `.env` MAIL_MAILER setting
- For development, emails are logged in `storage/logs/`
- In production, configure real mail service

### User can't login
- Verify email is verified (check `email_verified_at` in users table)
- Check user password is correct

### Email verification link invalid
- Link is signed with APP_KEY, ensure APP_KEY hasn't changed
- Link expires based on `email_verification_expires_in` setting

### Password reset not working
- Ensure password_reset_tokens table is created (run migrations)
- Token must be used within 60 minutes
- Ensure mail is configured

## Security Best Practices

1. **Password Security:**
   - Passwords are hashed using bcrypt
   - Password reset tokens are secure and time-limited

2. **Email Verification:**
   - Links are signed with APP_KEY
   - URLs contain user ID and verification hash
   - Prevents unauthorized email verification

3. **Session Security:**
   - Sessions stored in database
   - CSRF protection on all POST requests
   - Remember tokens for persistent login

4. **Rate Limiting:**
   - Email verification: 6 attempts per minute
   - Password reset: 1 attempt per minute
   - Prevents brute force attempts

## Next Steps

1. Configure real email service in production (Mailtrap, Mailgun, AWS SES, etc.)
2. Update MAIL_FROM_ADDRESS to your domain
3. Customize views in `resources/views/auth/` as needed
4. Customize validation rules in controllers as needed
5. Add additional user profile fields if required
6. Implement two-factor authentication if needed (separate package)

