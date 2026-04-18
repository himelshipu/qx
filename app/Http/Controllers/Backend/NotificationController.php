<?php

declare (strict_types = 1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\Admin\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class NotificationController extends Controller
{
    public function __construct(
        private readonly NotificationService $service,
    ) {
    }

    /**
     * Display all notifications for authenticated user
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $payload = $this->service->getIndexPayload($user, $request->only(['filter', 'type']));

        return view('backend.notifications.index', [
            'notifications' => $payload['notifications'],
            'stats' => $payload['stats'],
        ]);
    }

    /**
     * Get unread notifications via API (for dropdown)
     */
    public function getUnread(Request $request)
    {
        $user = Auth::user();

        return response()->json($this->service->getUnreadPayload($user));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $this->service->markAsRead($notification);

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
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $this->service->markAsUnread($notification);

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
        $this->service->markAllAsRead(Auth::user());

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
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $this->service->delete($notification);

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
        $this->service->clearAll(Auth::user());

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
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $this->service->markAsRead($notification);

        return redirect($notification->getActionUrl());
    }
}
