<?php

namespace App\Http\Controllers\Api\v1\Clinic;

use App\Http\Controllers\Controller;
use App\Models\core\ClinicNotification;
use App\Models\Scopes\ClinicScope;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use HttpResponses;

    /** List paginated notifications for the clinic. */
    public function index(Request $request): JsonResponse
    {
        $clinicId = $this->resolveClinicId($request);

        $notifications = ClinicNotification::withoutGlobalScope(ClinicScope::class)->where('clinic_id', $clinicId)
            ->latest()
            ->paginate($request->integer('per_page', 20));

        return $this->success([
            'data'        => $notifications->items(),
            'unread'      => ClinicNotification::withoutGlobalScope(ClinicScope::class)->where('clinic_id', $clinicId)->where('is_read', false)->count(),
            'total'       => $notifications->total(),
            'current_page' => $notifications->currentPage(),
            'last_page'   => $notifications->lastPage(),
        ]);
    }

    /** Mark a single notification as read. */
    public function markRead(int $id, Request $request): JsonResponse
    {
        $clinicId     = $this->resolveClinicId($request);
        $notification = ClinicNotification::withoutGlobalScope(ClinicScope::class)->where('clinic_id', $clinicId)->findOrFail($id);
        $notification->markAsRead();

        return $this->success(null, 'Notification marked as read.');
    }

    /** Mark all clinic notifications as read. */
    public function markAllRead(Request $request): JsonResponse
    {
        $clinicId = $this->resolveClinicId($request);

        ClinicNotification::withoutGlobalScope(ClinicScope::class)->where('clinic_id', $clinicId)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return $this->success(null, 'All notifications marked as read.');
    }

    /** Unread count only (for badge polling). */
    public function unreadCount(Request $request): JsonResponse
    {
        $clinicId = $this->resolveClinicId($request);

        return $this->success([
            'count' => ClinicNotification::withoutGlobalScope(ClinicScope::class)->where('clinic_id', $clinicId)->where('is_read', false)->count(),
        ]);
    }

    private function resolveClinicId(Request $request): int
    {
        $user = $request->user();

        $clinicId = $user?->clinic_id
            ?? $user?->doctor?->clinics()->value('clinics.id');

        abort_if(! $clinicId, 403, 'No clinic context found for this user.');

        return (int) $clinicId;
    }
}
