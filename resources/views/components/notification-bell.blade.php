@php
    $notifications = auth()->user()->notifications()->latest()->take(5)->get();
    $unreadCount = auth()->user()->unreadNotifications()->count();
@endphp

<x-dropdown align="right" width="80" content-classes="py-1 bg-slate-800 border border-slate-700">
    <x-slot name="trigger">
        <button class="relative inline-flex items-center justify-center w-10 h-10 rounded-md text-slate-300 hover:text-white hover:bg-slate-700 focus:outline-none transition">
            <i class="fa-solid fa-bell"></i>
            @if ($unreadCount > 0)
                <span class="absolute -top-1 -end-1 bg-rose-500 text-white text-[10px] font-bold rounded-full w-4 h-4 flex items-center justify-center">
                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                </span>
            @endif
        </button>
    </x-slot>

    <x-slot name="content">
        @forelse ($notifications as $notification)
            <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                @csrf
                <button type="submit"
                    class="w-full text-start px-4 py-2 text-sm transition {{ $notification->read_at ? 'text-slate-400' : 'text-white bg-slate-700/40' }} hover:bg-slate-700">
                    <p class="font-semibold">{{ $notification->data['title'] ?? 'إشعار' }}</p>
                    @if (!empty($notification->data['body']))
                        <p class="text-xs mt-1 text-slate-400 line-clamp-2">{{ $notification->data['body'] }}</p>
                    @endif
                </button>
            </form>
        @empty
            <p class="px-4 py-3 text-sm text-slate-500">لا توجد إشعارات</p>
        @endforelse

        <a href="{{ route('notifications.index') }}"
           class="block text-center px-4 py-2 text-sm text-indigo-400 hover:text-indigo-300 border-t border-slate-700">
            عرض كل الإشعارات
        </a>
    </x-slot>
</x-dropdown>