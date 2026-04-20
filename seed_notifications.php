<?php
require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Notification;
use App\Models\User;

try {
    $user = User::first();
    if (!$user) {
        echo "No users found!\n";
        exit;
    }

    echo "Testing notification system for user: " . $user->name . " (ID: " . $user->id . ")\n\n";

    // Create sample notifications
    Notification::create([
        'user_id'   => $user->id,
        'type'      => 'order',
        'title'     => 'New Order Received',
        'body'      => 'You have received a new order #12345 from a customer. Please review and process it.',
        'data_json' => json_encode([
            'action_url'  => '/dashboard/orders/1',
            'icon_class'  => 'shopping-cart',
            'color_class' => 'emerald'
        ]),
        'is_read'   => false
    ]);

    Notification::create([
        'user_id'   => $user->id,
        'type'      => 'payment',
        'title'     => 'Payment Received',
        'body'      => 'A payment of $500.00 has been received for order #12340.',
        'data_json' => json_encode([
            'action_url'  => '/dashboard/payments',
            'icon_class'  => 'credit-card',
            'color_class' => 'green'
        ]),
        'is_read'   => false
    ]);

    Notification::create([
        'user_id'   => $user->id,
        'type'      => 'campaign',
        'title'     => 'Campaign Updated',
        'body'      => 'Your campaign "Summer Promotion" has been updated by the admin.',
        'data_json' => json_encode([
            'action_url'  => '/dashboard/campaigns',
            'icon_class'  => 'megaphone',
            'color_class' => 'blue'
        ]),
        'is_read'   => true
    ]);

    Notification::create([
        'user_id'   => $user->id,
        'type'      => 'review',
        'title'     => 'New Review Posted',
        'body'      => '5-star review posted on your product "Premium Package".',
        'data_json' => json_encode([
            'action_url'  => '/dashboard/reviews',
            'icon_class'  => 'star',
            'color_class' => 'yellow'
        ]),
        'is_read'   => false
    ]);

    $total  = Notification::where('user_id', $user->id)->count();
    $unread = Notification::where('user_id', $user->id)->where('is_read', false)->count();

    echo "\n✓ Sample notifications created successfully!\n";
    echo "Total notifications: " . $total . "\n";
    echo "Unread notifications: " . $unread . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
