<?php

namespace App\Http\Controllers\Web\Clinic;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Clinic\Concerns\ResolvesClinic;
use App\Models\core\ClinicNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    use ResolvesClinic;

    /**
     * Navbar polling endpoint — returns unread count + latest 5 notifications.
     */
    public function check(): JsonResponse
    {
        $clinicId = $this->resolveClinic()->id;

        $unread = ClinicNotification::where('clinic_id', $clinicId)
            ->where('is_read', false)
            ->count();

        $latest = ClinicNotification::where('clinic_id', $clinicId)
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($n) => [
                'id'         => $n->id,
                'type'       => $n->type,
                'title'      => $n->title,
                'message'    => $n->message,
                'is_read'    => $n->is_read,
                'created_at' => $n->created_at->diffForHumans(),
                'data'       => $n->data,
            ]);

        return response()->json([
            'count'         => $unread,
            'notifications' => $latest,
        ]);
    }

    /**
     * Full notifications page (paginated).
     */
    public function index(Request $request): View
    {
        $clinicId = $this->resolveClinic()->id;

        $notifications = ClinicNotification::where('clinic_id', $clinicId)
            ->latest()
            ->paginate(20);

        $unreadCount = ClinicNotification::where('clinic_id', $clinicId)
            ->where('is_read', false)
            ->count();

        return view('clinic.notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Mark a single notification as read.
     */
    public function markRead(ClinicNotification $notification): JsonResponse
    {
        $clinicId = $this->resolveClinic()->id;
        abort_if($notification->clinic_id !== $clinicId, 403);

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all clinic notifications as read.
     */
    public function markAllRead(): JsonResponse
    {
        $clinicId = $this->resolveClinic()->id;

        ClinicNotification::where('clinic_id', $clinicId)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['success' => true]);
    }
}
