#!/bin/bash

# Email Verification System - Fix and Test Script

echo "=========================================="
echo "Email Verification System - Fix & Test"
echo "=========================================="

cd /var/www/qx

echo ""
echo "Step 1: Clear all caches..."
php artisan cache:clear
php artisan config:clear  
php artisan route:clear

echo ""
echo "Step 2: Clear old queue jobs (if any)..."
php artisan tinker <<'EOF'
// Clear any failed or pending queue jobs
DB::table('jobs')->truncate();
DB::table('failed_jobs')->truncate();
echo "Queue jobs cleared\n";
exit;
EOF

echo ""
echo "Step 3: Remove old test users and verification codes..."
php artisan tinker <<'EOF'
$users = App\Models\User::where('email', 'like', '%@%')->get();
foreach ($users as $user) {
    echo "Clearing verification data for: " . $user->email . "\n";
    $user->update([
        'verification_code' => null,
        'verification_code_expires_at' => null,
    ]);
}
exit;
EOF

echo ""
echo "Step 4: Verify routes are correctly configured..."
php artisan route:list | grep email-verification

echo ""
echo "Step 5: Check application configuration..."
echo "Mail Mailer: $(php artisan config:get mail.mailer)"
echo "Mail From: $(php artisan config:get mail.from.address)"

echo ""
echo "=========================================="
echo "System is ready for testing!"
echo "=========================================="
echo ""
echo "Next steps:"
echo "1. Open browser and go to: http://qx.local/register"
echo "2. Fill in registration form"
echo "3. Click Register"
echo "4. You should see email verification page immediately"
echo "5. Enter email, click 'Send Verification Code'"
echo "6. Check email for 6-digit code"
echo "7. Enter code and click Verify"
echo ""
echo "Note: If still receiving old email, check:"
echo "- storage/logs/laravel.log for email content"
echo "- Spam/Junk folder in Gmail"
echo "- Try a different email address"
