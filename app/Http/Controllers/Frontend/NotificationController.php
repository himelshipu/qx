<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications for the authenticated user.
     */
    public function index(Request $request): View
    {
        $query = Notification::where('user_id', auth()->id());

        // Filter by status
        if ($request->filled('status') && in_array($request->status, ['read', 'unread'])) {
            $query->where('is_read', $request->status === 'read');
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Get total and unread counts
        $stats = [
            'total'  => Notification::where('user_id', auth()->id())->count(),
            'unread' => Notification::where('user_id', auth()->id())->where('is_read', false)->count(),
            'read'   => Notification::where('user_id', auth()->id())->where('is_read', true)->count()
        ];

        $notifications = $query->latest('created_at')->paginate(15);

        return view('frontend.notifications.index', compact('notifications', 'stats'));
    }

    /**
     * Get unread notifications as JSON (for AJAX calls).
     */
    public function getUnread(): JsonResponse
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->latest('created_at')
            ->take(8)
            ->get()
            ->map(function ($notification) {
                // Render SVG icon component to HTML so the frontend can inject it via x-html
                $iconHtml = null;
                try {
                    $iconView = 'components.icons.' . $notification->getIcon();
                    if (view()->exists($iconView)) {
                        $iconHtml = view($iconView)->render();
                    } else {
                        $iconHtml = view('components.icons.bell')->render();
                    }
                } catch (\Throwable $e) {
                    $iconHtml = '';
                }

                return [
                    'id'         => $notification->id,
                    'type'       => $notification->type,
                    'title'      => $notification->title,
                    'body'       => $notification->body,
                    'icon'       => $iconHtml,
                    'color'      => $notification->getColor(),
                    'action_url' => $notification->getActionUrl(),
                    'created_at' => $notification->created_at->diffForHumans(),
                    'is_read'    => $notification->is_read,
                ];
            });

        $unreadCount = Notification::where('user_id', auth()->id())->where('is_read', false)->count();
        $hasUnread   = $unreadCount > 0;

        return response()->json(compact('notifications', 'unreadCount', 'hasUnread'));
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(Notification $notification): JsonResponse
    {
        // Verify ownership
        if ($notification->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $notification->markAsRead();

        return response()->json(['success' => true, 'message' => 'Notification marked as read']);
    }

    /**
     * Mark a notification as unread.
     */
    public function markAsUnread(Notification $notification): JsonResponse
    {
        // Verify ownership
        if ($notification->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $notification->markAsUnread();

        return response()->json(['success' => true, 'message' => 'Notification marked as unread']);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): JsonResponse
    {
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['success' => true, 'message' => 'All notifications marked as read']);
    }

    /**
     * Delete a notification.
     */
    public function destroy(Notification $notification): JsonResponse
    {
        // Verify ownership
        if ($notification->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $notification->delete();

        return response()->json(['success' => true, 'message' => 'Notification deleted']);
    }

    /**
     * Clear all notifications.
     */
    public function clearAll(): JsonResponse
    {
        Notification::where('user_id', auth()->id())->delete();

        return response()->json(['success' => true, 'message' => 'All notifications cleared']);
    }

    /**
     * View and redirect to notification action URL.
     */
    public function show(Notification $notification)
    {
        // Verify ownership
        if ($notification->user_id !== auth()->id()) {
            return redirect()->route('home')->with('error', 'Unauthorized access');
        }

        // Mark as read
        if (!$notification->is_read) {
            $notification->markAsRead();
        }

        // Redirect to action URL if available
        if ($actionUrl = $notification->getActionUrl()) {
            return redirect($actionUrl);
        }

        // Fallback to notifications list

        return redirect()->route('frontend.notifications.index');
    }
}
