# Authentication Quick Reference Card

## Essential Commands

```bash
# Start development server
php artisan serve --host=0.0.0.0 --port=8000

# Run migrations
php artisan migrate

# Build frontend assets
npm run build

# Watch for changes (frontend)
npm run dev

# Access Laravel Tinker (shell)
php artisan tinker
```

## Routes

| Method | Route | Description | Auth Required |
|--------|-------|-------------|---------------|
| GET | `/` | Welcome page | No |
| GET | `/register` | Registration form | No |
| POST | `/register` | Create new user | No |
| GET | `/login` | Login form | No |
| POST | `/login` | Authenticate user | No |
| GET | `/forgot-password` | Password reset form | No |
| POST | `/forgot-password` | Send reset link | No |
| GET | `/reset-password/{token}` | Reset password form | No |
| POST | `/reset-password` | Update password | No |
| GET | `/dashboard` | User dashboard | Yes + Verified |
| GET | `/verify-email` | Verification prompt | Yes |
| GET | `/verify-email/{id}/{hash}` | Verify email | Yes + Signed |
| POST | `/email/verification-notification` | Resend email | Yes |
| GET | `/profile` | Edit profile | Yes |
| PUT | `/profile` | Update profile | Yes |
| DELETE | `/profile` | Delete account | Yes |
| PUT | `/password` | Change password | Yes |
| POST | `/logout` | Logout | Yes |

## Tinker Commands

```php
# Create users
User::create(['name' => 'John', 'email' => 'john@example.com', 'password' => Hash::make('password123')]);
User::factory(5)->create();
User::factory(3)->create(['email_verified_at' => now()]);

# Get users
User::all();
User::where('email', 'user@example.com')->first();

# Update user
$user = User::first();
$user->update(['name' => 'New Name']);
$user->markEmailAsVerified();

# Send notification
$user->sendEmailVerificationNotification();

# Delete users
User::first()->delete();
User::where('email', 'like', '%@example.com')->delete();

# Check email verified
$user->hasVerifiedEmail();

# Reset password
$user->update(['password' => Hash::make('newpassword123')]);

# Sessions
DB::table('sessions')->get();
DB::table('sessions')->where('user_id', 1)->delete();
DB::table('sessions')->delete(); // Clear all
```

## Email Logging

```bash
# View all logs
tail -100 storage/logs/laravel.log

# View verification emails
tail -100 storage/logs/laravel.log | grep -i verify

# View password reset emails
tail -100 storage/logs/laravel.log | grep -i password

# View all message IDs (sent emails)
tail -100 storage/logs/laravel.log | grep "message-id"

# Search for specific email
tail -100 storage/logs/laravel.log | grep "john@example.com"
```

## Environment Variables (.env)

```env
# Application
APP_NAME=Laravel
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=qx
DB_USERNAME=root
DB_PASSWORD=password

# Mail (Development - uses log driver)
MAIL_MAILER=log
MAIL_FROM_ADDRESS=noreply@qx.local
MAIL_FROM_NAME="QX Application"

# Session
SESSION_DRIVER=database

# Cache & Queue
CACHE_STORE=database
QUEUE_CONNECTION=database
```

## File Locations

| What | Location |
|------|----------|
| Login view | `resources/views/auth/login.blade.php` |
| Register view | `resources/views/auth/register.blade.php` |
| Password reset | `resources/views/auth/reset-password.blade.php` |
| Email verification | `resources/views/auth/verify-email.blade.php` |
| Profile | `resources/views/profile/edit.blade.php` |
| User model | `app/Models/User.php` |
| Auth routes | `routes/auth.php` |
| Web routes | `routes/web.php` |
| Controllers | `app/Http/Controllers/Auth/` |
| Listeners | `app/Listeners/` |

## Common Issues & Solutions

| Problem | Solution |
|---------|----------|
| User can't access dashboard | Verify email first at `/verify-email` |
| Emails not sending | Check `storage/logs/laravel.log` |
| Reset link expired | Tokens expire after 60 min, request new link |
| Can't login | Check email/password, verify email exists |
| Session not saving | Ensure `php artisan migrate` was run |
| Frontend styling broken | Run `npm run build` |
| PHP errors | Check `storage/logs/` for error logs |

## Database Tables

### users
- `id` - User ID
- `name` - Full name
- `email` - Email address (unique)
- `email_verified_at` - Verification timestamp
- `password` - Hashed password
- `remember_token` - Remember me token
- `created_at`, `updated_at` - Timestamps

### password_reset_tokens
- `email` - User email (primary key)
- `token` - Reset token
- `created_at` - Creation timestamp
- *Expires after 60 minutes*

### sessions
- `id` - Session ID (primary key)
- `user_id` - User ID reference
- `ip_address` - IP address
- `user_agent` - Browser info
- `payload` - Session data
- `last_activity` - Last activity timestamp

## Authentication Middleware

```php
// Use in routes
Route::middleware('auth')->group(function () {
    // Protected routes
});

Route::middleware('verified')->group(function () {
    // Routes requiring verified email
});

Route::middleware('guest')->group(function () {
    // Routes for not-logged-in users
});
```

## Testing Checklist

- [ ] Register new user
- [ ] Verify email via link in logs
- [ ] Login with credentials
- [ ] Access dashboard (must be verified)
- [ ] Update profile
- [ ] Change password
- [ ] Logout
- [ ] Test forgot password flow
- [ ] Reset password with token
- [ ] Login with new password
- [ ] Test with multiple users

## Key Features

- ✅ Session-based authentication
- ✅ Email verification with signed links
- ✅ Password reset with tokens (60 min expiry)
- ✅ Database session storage
- ✅ CSRF protection
- ✅ Rate limiting (6 email/min, 1 reset/min)
- ✅ Password hashing (bcrypt)
- ✅ Profile management
- ✅ Remember me functionality

## Documentation Files

1. **README_AUTH.md** - Quick overview
2. **AUTHENTICATION.md** - Complete documentation
3. **TESTING_AUTHENTICATION.md** - Testing guide
4. **EMAIL_CONFIGURATION.md** - Email setup guide
5. **BREEZE_INSTALLATION_SUMMARY.md** - Technical details

## Configuration Files

- `config/auth.php` - Authentication settings
- `config/mail.php` - Mail configuration
- `bootstrap/providers.php` - Service providers
- `.env` - Environment variables

## Useful npm Commands

```bash
npm install       # Install dependencies
npm run dev       # Watch for changes
npm run build     # Build for production
npm audit         # Check for vulnerabilities
npm audit fix     # Fix vulnerabilities
```

## Useful php artisan Commands

```bash
php artisan migrate              # Run migrations
php artisan migrate:status       # Check migration status
php artisan route:list           # List all routes
php artisan tinker               # Interactive shell
php artisan tail                 # Watch logs
php artisan config:clear         # Clear config cache
php artisan cache:clear          # Clear all caches
php artisan queue:work           # Start queue worker
```

## Remember!

- 🔒 Passwords are hashed with bcrypt
- 📧 Emails in dev go to `storage/logs/laravel.log`
- ⏱️ Email verification links are signed URLs
- 🔐 Password reset tokens expire after 60 minutes
- 💾 Sessions are stored in database
- 🛡️ CSRF tokens required on all POST requests
- ✅ Dashboard requires logged in + verified email

---
**Quick Tip:** Keep this file handy for quick reference while developing!
