# Email Configuration Guide - Production Setup

## Current Development Configuration

**Current Setting:** `MAIL_MAILER=log`

In development, all emails are written to `storage/logs/laravel.log`. This is perfect for testing without a real mail server.

## Email Configuration Options

### Option 1: SMTP (Most Common)

Update your `.env` file:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_SCHEME=tls
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Your App Name"
```

#### Popular SMTP Services:
- **Mailtrap:** Free tier for development/testing
- **Gmail:** Using app-specific password
- **SendGrid:** Free tier available
- **AWS SES:** Low cost at scale

### Option 2: Mailgun

Update your `.env` file:

```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=mg.yourdomain.com
MAILGUN_SECRET=key-xxxxxxxxxxxxxxxx
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Your App Name"
```

### Option 3: SendGrid

Update your `.env` file:

```env
MAIL_MAILER=sendgrid
SENDGRID_API_KEY=SG.xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Your App Name"
```

### Option 4: AWS SES (Simple Email Service)

Update your `.env` file:

```env
MAIL_MAILER=ses
AWS_ACCESS_KEY_ID=xxxxxxxxxxxxxxxxxxxx
AWS_SECRET_ACCESS_KEY=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
AWS_DEFAULT_REGION=us-east-1
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Your App Name"
```

### Option 5: Postmark

Update your `.env` file:

```env
MAIL_MAILER=postmark
POSTMARK_TOKEN=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Your App Name"
```

### Option 6: Resend

Update your `.env` file:

```env
MAIL_MAILER=resend
RESEND_API_KEY=re_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Your App Name"
```

## Step-by-Step Setup Examples

### Example 1: Setting up Mailtrap (Easy - Recommended for Testing)

1. Go to https://mailtrap.io
2. Sign up for free account
3. Create a test inbox
4. Go to "SMTP Settings"
5. Copy credentials:
   - Host: `smtp.mailtrap.io`
   - Port: `2525` (or 465 for TLS)
   - Username: (your username)
   - Password: (your password)

6. Update `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_SCHEME=tls
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_FROM_ADDRESS=noreply@qx.local
MAIL_FROM_NAME="QX Application"
```

7. Test by registering a user and checking Mailtrap inbox

### Example 2: Setting up Gmail (Uses App Password)

1. Enable 2-Factor Authentication on Google Account
2. Generate App Password: https://myaccount.google.com/apppasswords
3. Select "Mail" and "Windows Computer" (or your device)
4. Google will generate a 16-character password
5. Update `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_SCHEME=tls
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_16_char_app_password
MAIL_FROM_ADDRESS=your_email@gmail.com
MAIL_FROM_NAME="QX Application"
```

6. Test registration to receive verification email

### Example 3: Setting up SendGrid

1. Go to https://sendgrid.com
2. Sign up for free account (get 100 emails/day)
3. Create API key: Settings → API Keys
4. Update `.env`:
```env
MAIL_MAILER=sendgrid
SENDGRID_API_KEY=SG.your_api_key_here
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="QX Application"
```

5. Test registration

## Email Templates Used

The application sends emails for:

1. **Email Verification** - `Illuminate\Auth\Notifications\VerifyEmail`
2. **Password Reset** - `Illuminate\Auth\Notifications\ResetPassword`

These are built-in Laravel notifications. You can customize them if needed:

```bash
# Publish email templates (optional)
php artisan vendor:publish --tag=laravel-notifications
```

## Testing Email Configuration

### Test 1: Send Test Email via Tinker
```bash
php artisan tinker
```

```php
Mail::raw('Test email body', function ($message) {
    $message->to('test@example.com')
            ->subject('Test Email');
});
```

### Test 2: Register a User
1. Go to `/register`
2. Fill form with test email
3. Check your email inbox for verification email
4. Verify email worked if you receive it

### Test 3: Test Password Reset
1. Go to `/forgot-password`
2. Enter email
3. Check inbox for reset link
4. Verify password reset works

### Test 4: Check Mail Logs
```bash
# View sent emails
tail -50 storage/logs/laravel.log | grep -i "message-id"

# View errors
grep -i "mail.*error" storage/logs/laravel.log
```

## Troubleshooting Email Issues

### Issue: "Swift_TransportException Connection refused"
**Cause:** SMTP server unreachable
**Solution:** 
- Check host and port are correct
- Ensure firewall allows connection to SMTP port
- Test with Mailtrap first

### Issue: "Authentication failed"
**Cause:** Wrong username/password
**Solution:**
- Double-check credentials in .env
- Regenerate API keys in mail service dashboard
- Clear config cache: `php artisan config:clear`

### Issue: Emails sent but not received
**Cause:** Wrong FROM address or email domain not verified
**Solution:**
- Verify sender email in mail service
- For SendGrid/Mailgun: verify domain
- Check spam folder
- Verify CNAME records if using custom domain

### Issue: "No suitable servers"
**Cause:** Hostname resolution failed
**Solution:**
- Check DNS resolution: `nslookup smtp.mailtrap.io`
- Verify server is online
- Check firewall rules

### Issue: Emails take long time
**Cause:** Network latency or queue configuration
**Solution:**
- If using queue, ensure queue worker is running: `php artisan queue:work`
- Check QUEUE_CONNECTION in .env (currently set to `database`)

## Email Queue Configuration

Currently emails are sent synchronously. For high traffic, you can queue emails:

### Enable Email Queuing

Update mail config in `config/mail.php`:

```php
'allow_multiple_recipients' => true,
```

Or use the notification's `queue` method in controllers.

### Start Queue Worker

```bash
php artisan queue:work
```

This will process mailed jobs in the background.

## Production Checklist

- [ ] Choose email service (Mailgun, SendGrid, etc.)
- [ ] Create account and get credentials
- [ ] Update `.env` with credentials
- [ ] Update `MAIL_FROM_ADDRESS` to your domain
- [ ] Clear config cache: `php artisan config:clear`
- [ ] Test by registering a user
- [ ] Verify verification email is received
- [ ] Test password reset
- [ ] Monitor email delivery in dashboard

## Environment-Specific Configuration

You can use different mail configurations for different environments:

### .env.example (Shared with team)
```env
MAIL_MAILER=log
MAIL_FROM_ADDRESS=noreply@qx.local
MAIL_FROM_NAME="QX Application"
```

### .env (Local development)
```env
MAIL_MAILER=mailtrap
MAIL_HOST=smtp.mailtrap.io
# ... other local settings
```

### .env.production (Production)
```env
MAIL_MAILER=sendgrid
SENDGRID_API_KEY=SG.xxxxx
# ... production settings
```

## Laravel Mail Events

Monitor email sending:

```php
// In AppServiceProvider.php boot() method
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Event;

public function boot()
{
    Event::listen(MessageSending::class, function ($event) {
        \Log::info('Email sent to: ' . $event->message->getTo());
    });
}
```

## Common Email Service Costs

| Service | Free Tier | Typical Cost |
|---------|-----------|--------------|
| Mailtrap | 5,000/month | Free development |
| SendGrid | 100/day | $10-200/month |
| Mailgun | 5,000/month | $10-400/month |
| AWS SES | 62,000/month* | $0.10 per 1,000 |
| Resend | 100/day | $20-200/month |

*when receiving emails, lower for sending only

## Additional Resources

- [Laravel Mail Documentation](https://laravel.com/docs/mail)
- [Mailtrap](https://mailtrap.io)
- [SendGrid](https://sendgrid.com)
- [Mailgun](https://mailgun.com)
- [AWS SES](https://aws.amazon.com/ses/)

## Support

For issues with specific email providers, consult their documentation or submit a support ticket to their platform.
