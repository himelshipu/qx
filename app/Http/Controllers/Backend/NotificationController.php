<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display all notifications for authenticated user
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $notifications = Notification::where('user_id', $user->id)
            ->when($request->filter, function ($query, $filter) {
                if ($filter === 'unread') {
                    return $query->unread();
                } elseif ($filter === 'read') {
                    return $query->read();
                }
            })
            ->when($request->type, function ($query, $type) {
                return $query->byType($type);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Count statistics
        $stats = [
            'total'  => Notification::where('user_id', $user->id)->count(),
            'unread' => Notification::where('user_id', $user->id)->unread()->count(),
            'read'   => Notification::where('user_id', $user->id)->read()->count()
        ];

        return view('backend.notifications.index', compact('notifications', 'stats'));
    }

    /**
     * Get unread notifications via API (for dropdown)
     */
    public function getUnread(Request $request)
    {
        $user = Auth::user();

        $notifications = Notification::where('user_id', $user->id)
            ->unread()
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        $unreadCount = Notification::where('user_id', $user->id)->unread()->count();

        return response()->json([
            'notifications' => $notifications,
            'unreadCount'   => $unreadCount,
            'hasUnread'     => $unreadCount > 0
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, Notification $notification)
    {
        // Check authorization
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->markAsRead();

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Notification marked as read');
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread(Request $request, Notification $notification)
    {
        // Check authorization
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->markAsUnread();

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Notification marked as unread');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        $user = Auth::user();

        Notification::where('user_id', $user->id)
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'All notifications marked as read');
    }

    /**
     * Delete a notification
     */
    public function destroy(Request $request, Notification $notification)
    {
        // Check authorization
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Notification deleted');
    }

    /**
     * Clear all notifications
     */
    public function clearAll(Request $request)
    {
        $user = Auth::user();

        Notification::where('user_id', $user->id)->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'All notifications cleared');
    }

    /**
     * Get notification details (redirect to action URL)
     */
    public function show(Notification $notification)
    {
        // Check authorization
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        // Mark as read before redirecting
        $notification->markAsRead();

        // Redirect to action URL

        return redirect($notification->getActionUrl());
    }
}
