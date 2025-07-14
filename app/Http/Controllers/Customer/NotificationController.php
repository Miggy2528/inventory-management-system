<?php

namespace App\Http\Controllers\Customer;

use App\Models\CustomerNotification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    /**
     * Get customer's notifications
     */
    public function index(Request $request): JsonResponse
    {
        $customer = $request->user();
        $perPage = $request->get('per_page', 15);
        $type = $request->get('type');
        $isRead = $request->get('is_read');

        $query = $customer->notifications();

        if ($type) {
            $query->where('type', $type);
        }

        if ($isRead !== null) {
            $query->where('is_read', $isRead);
        }

        $notifications = $query->latest()->paginate($perPage);

        return response()->json($notifications);
    }

    /**
     * Get specific notification
     */
    public function show(Request $request, CustomerNotification $notification): JsonResponse
    {
        $customer = $request->user();

        // Ensure customer can only view their own notifications
        if ($notification->customer_id !== $customer->id) {
            return response()->json(['message' => 'Notification not found'], 404);
        }

        // Mark as read when viewed
        if (!$notification->isRead()) {
            $notification->markAsRead();
        }

        return response()->json($notification);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, CustomerNotification $notification): JsonResponse
    {
        $customer = $request->user();

        // Ensure customer can only mark their own notifications
        if ($notification->customer_id !== $customer->id) {
            return response()->json(['message' => 'Notification not found'], 404);
        }

        $notification->markAsRead();

        return response()->json([
            'message' => 'Notification marked as read',
            'notification' => $notification->fresh(),
        ]);
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread(Request $request, CustomerNotification $notification): JsonResponse
    {
        $customer = $request->user();

        // Ensure customer can only mark their own notifications
        if ($notification->customer_id !== $customer->id) {
            return response()->json(['message' => 'Notification not found'], 404);
        }

        $notification->markAsUnread();

        return response()->json([
            'message' => 'Notification marked as unread',
            'notification' => $notification->fresh(),
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $customer = $request->user();

        $customer->unreadNotifications()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json([
            'message' => 'All notifications marked as read',
        ]);
    }

    /**
     * Delete notification
     */
    public function destroy(Request $request, CustomerNotification $notification): JsonResponse
    {
        $customer = $request->user();

        // Ensure customer can only delete their own notifications
        if ($notification->customer_id !== $customer->id) {
            return response()->json(['message' => 'Notification not found'], 404);
        }

        $notification->delete();

        return response()->json([
            'message' => 'Notification deleted successfully',
        ]);
    }

    /**
     * Get notification statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        $customer = $request->user();

        $stats = [
            'total_notifications' => $customer->notifications()->count(),
            'unread_notifications' => $customer->unreadNotifications()->count(),
            'read_notifications' => $customer->notifications()->read()->count(),
            'recent_notifications' => $customer->notifications()->recent()->count(),
            'notification_types' => $customer->notifications()
                ->selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->get(),
        ];

        return response()->json($stats);
    }

    /**
     * Get unread notifications count (for real-time updates)
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $customer = $request->user();

        $count = $customer->unreadNotifications()->count();

        return response()->json([
            'unread_count' => $count,
        ]);
    }

    /**
     * Get recent notifications (last 5)
     */
    public function recent(Request $request): JsonResponse
    {
        $customer = $request->user();

        $notifications = $customer->notifications()
            ->latest()
            ->limit(5)
            ->get();

        return response()->json($notifications);
    }
} 