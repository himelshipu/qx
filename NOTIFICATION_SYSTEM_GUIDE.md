# Notification System - Usage Guide

## ✅ Quick Checklist Status

- [x] Create Notification model and migration
- [x] Create NotificationController
- [x] Create notification views (list and page)
- [x] Update routes with notification endpoints
- [x] Update header dropdown with real data
- [x] Add notification sidebar link
- [x] Create notification events/triggers

**STATUS: ALL TODO ITEMS COMPLETED ✓**

## 🐛 Issues Fixed

- Fixed missing route parameter error in notification dropdown
- Routes now correctly generate with notification IDs as parameters

## 📚 How to Use the Notification System

### 1. Using the HasNotifications Trait

Add the trait to any model that needs to trigger notifications:

```php
use App\Traits\HasNotifications;

class Order extends Model
{
    use HasNotifications;
}
```

### 2. Notifying a Single User

```php
$order = Order::find(1);

// Notify a specific user
$order->notifyUser(1, 'order', 'New Order', 'Order #123 has been created', [
    'action_url' => '/dashboard/orders/1',
    'color_class' => 'emerald'
]);

// Or pass a User model
$order->notifyUser($user, 'order', 'New Order', 'Your order has been confirmed', [
    'action_url' => route('dashboard.orders.show', $order)
]);
```

### 3. Notifying Multiple Users

```php
$order->notifyUsers([1, 2, 3], 'order', 'New Order Received', 'Process the new order');
```

### 4. Notifying by Role

```php
// Notify all admins
$order->notifyAdmins('order', 'New Order Alert', 'A customer order needs approval');

// Notify by custom role
$order->notifyRole('moderator', 'order', 'Review Required', 'Order requires moderation');
```

### 5. Creating Notifications in Events/Jobs

```php
// In an event listener or job
$order = Order::find($orderId);
$order->notifyUser(
    auth()->id(),
    'order',
    'Order Confirmation',
    'Your order has been confirmed. Track it here.',
    ['action_url' => route('dashboard.orders.show', $order)]
);
```

## 📋 Notification Types Supported

- `order` - Order-related notifications (emerald)
- `payment` - Payment notifications (green)
- `campaign` - Campaign updates (blue)
- `message` - Messages (indigo)
- `review` - Review notifications (yellow)
- `payout` - Payout notifications (purple)

## 🎨 Notification Colors

Colors are automatically set by type, but can be overridden in data_json:

```php
[
    'action_url' => '/dashboard/orders/1',
    'icon_class' => 'shopping-cart',
    'color_class' => 'emerald'  // blue, green, red, yellow, purple, indigo, emerald
]
```

## 🔌 API Endpoints

### Get Unread Notifications (for dropdown)

```
GET /dashboard/notifications/api/unread
```

Response:

```json
{
    "notifications": [...],
    "unreadCount": 5,
    "hasUnread": true
}
```

### Mark as Read

```
POST /dashboard/notifications/{notification}/mark-as-read
```

### Mark All as Read

```
POST /dashboard/notifications/mark-all-as-read
```

### Delete Notification

```
DELETE /dashboard/notifications/{notification}
```

### Clear All Notifications

```
DELETE /dashboard/notifications/clear-all
```

## 📍 Routes

All notification routes are prefixed with `/dashboard`:

- `GET /notifications` - View all notifications
- `GET /notifications/api/unread` - Get unread notifications (AJAX)
- `POST /notifications/{notification}/mark-as-read` - Mark as read
- `POST /notifications/{notification}/mark-as-unread` - Mark as unread
- `POST /notifications/mark-all-as-read` - Mark all as read
- `DELETE /notifications/{notification}` - Delete notification
- `DELETE /notifications/clear-all` - Clear all
- `GET /notifications/{notification}/show` - View and redirect

## 📦 Database Schema

The `notifications` table contains:

- `id` - Primary key
- `user_id` - User receiving the notification
- `type` - Notification type (order, payment, campaign, etc.)
- `title` - Notification title
- `body` - Notification message
- `data_json` - Additional data (action_url, icons, colors)
- `is_read` - Read status
- `read_at` - When marked as read
- `created_at`, `updated_at` - Timestamps
- `deleted_at` - Soft delete timestamp

## 🎯 Features Included

✅ Real-time notification fetching via AJAX
✅ Unread count badge in header dropdown
✅ Color-coded notifications by type
✅ Type-specific icons
✅ Mark as read/unread functionality
✅ View all notifications page with statistics
✅ Pagination support
✅ Soft delete support
✅ Polymorphic relationships (can relate to any model)
✅ Authorization checks on all operations
✅ RESTful API endpoints

## 🔒 Security Features

- All routes require authentication
- User can only view/modify their own notifications
- CSRF token protection on all state-changing endpoints
- Laravel's built-in authorization checks
