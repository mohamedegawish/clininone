@extends('layouts.app')

@section('title', __('Notifications'))

@section('content')
<div class="max-w-3xl mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
            {{ __('Notifications') }}
            @if($unreadCount > 0)
                <span class="ml-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                    {{ $unreadCount }}
                </span>
            @endif
        </h1>
        @if($unreadCount > 0)
        <button
            id="markAllReadBtn"
            class="text-sm text-blue-600 hover:underline dark:text-blue-400"
        >
            {{ __('Mark all as read') }}
        </button>
        @endif
    </div>

    <div class="space-y-3" id="notificationsList">
        @forelse($notifications as $notification)
        <div
            id="notif-{{ $notification->id }}"
            class="flex items-start gap-4 p-4 rounded-xl border shadow-sm transition
                {{ $notification->is_read
                    ? 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700'
                    : 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-700' }}"
        >
            {{-- Icon --}}
            <div class="shrink-0 mt-1">
                @if($notification->type === 'appointment')
                    <span class="text-blue-500 text-xl">📅</span>
                @elseif($notification->type === 'payment')
                    <span class="text-green-500 text-xl">💰</span>
                @else
                    <span class="text-gray-400 text-xl">🔔</span>
                @endif
            </div>

            {{-- Content --}}
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                    {{ $notification->title }}
                </p>
                @if($notification->message)
                <p class="text-sm text-gray-600 dark:text-gray-300 mt-0.5">
                    {{ $notification->message }}
                </p>
                @endif
                <p class="text-xs text-gray-400 mt-1">
                    {{ $notification->created_at->diffForHumans() }}
                </p>
            </div>

            {{-- Read indicator --}}
            @if(! $notification->is_read)
            <button
                class="shrink-0 mark-read-btn text-xs text-blue-600 hover:underline dark:text-blue-400"
                data-id="{{ $notification->id }}"
            >
                {{ __('Mark read') }}
            </button>
            @endif
        </div>
        @empty
        <div class="text-center py-16 text-gray-400">
            <span class="text-5xl">🔕</span>
            <p class="mt-3 text-base">{{ __('No notifications yet.') }}</p>
        </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $notifications->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Mark single notification as read
    document.querySelectorAll('.mark-read-btn').forEach(btn => {
        btn.addEventListener('click', async () => {
            const id = btn.dataset.id;
            await fetch(`/clinic/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            });
            const row = document.getElementById(`notif-${id}`);
            row.classList.remove('bg-blue-50', 'dark:bg-blue-900/20', 'border-blue-200', 'dark:border-blue-700');
            row.classList.add('bg-white', 'dark:bg-gray-800', 'border-gray-200', 'dark:border-gray-700');
            btn.remove();
        });
    });

    // Mark all as read
    const markAllBtn = document.getElementById('markAllReadBtn');
    if (markAllBtn) {
        markAllBtn.addEventListener('click', async () => {
            await fetch('/clinic/notifications/read-all', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            });
            location.reload();
        });
    }
});
</script>
@endpush
