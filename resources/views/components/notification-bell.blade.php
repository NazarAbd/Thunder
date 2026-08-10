@php
    $notifications = auth()->user()->notifications()->latest()->take(5)->get();
    $unreadCount = auth()->user()->unreadNotifications()->count();
@endphp

<div
    x-data="{
        notifications: @js($notifications->map(fn ($n) => [
            'id' => $n->id,
            'title' => $n->data['title'] ?? 'إشعار',
            'body' => $n->data['body'] ?? null,
            'read' => ! is_null($n->read_at),
        ])),
        unreadCount: {{ $unreadCount }},
        listening: false,
        readUrl(id) {
            return '{{ url('notifications') }}/' + id + '/read';
        },
        listen() {
            if (this.listening || ! window.Echo) return;
            this.listening = true;
            window.Echo.private('App.Models.User.{{ auth()->id() }}')
                .notification((notification) => {
                    this.notifications.unshift({
                        id: notification.id,
                        title: notification.title ?? 'إشعار',
                        body: notification.body ?? null,
                        read: false,
                    });
                    if (this.notifications.length > 5) this.notifications.pop();
                    this.unreadCount++;
                });
        }
    }"
    x-init="listen(); window.addEventListener('EchoReady', () => listen())"
>
    <x-dropdown align="right" width="80" content-classes="py-1 bg-slate-800 border border-slate-700">
        <x-slot name="trigger">
            <button class="relative inline-flex items-center justify-center w-10 h-10 rounded-md text-slate-300 hover:text-white hover:bg-slate-700 focus:outline-none transition">
                <i class="fa-solid fa-bell"></i>
                <span x-show="unreadCount > 0" class="absolute -top-1 -end-1 bg-rose-500 text-white text-[10px] font-bold rounded-full w-4 h-4 flex items-center justify-center">
                    <span x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
                </span>
            </button>
        </x-slot>

        <x-slot name="content">
            <template x-for="notification in notifications" :key="notification.id">
                <form method="POST" :action="readUrl(notification.id)">
                    @csrf
                    <button type="submit"
                        class="w-full text-start px-4 py-2 text-sm transition hover:bg-slate-700"
                        :class="notification.read ? 'text-slate-400' : 'text-white bg-slate-700/40'">
                        <p class="font-semibold" x-text="notification.title"></p>
                        <p class="text-xs mt-1 text-slate-400 line-clamp-2" x-show="notification.body" x-text="notification.body"></p>
                    </button>
                </form>
            </template>

            <p class="px-4 py-3 text-sm text-slate-500" x-show="notifications.length === 0">لا توجد إشعارات</p>

            <a href="{{ route('notifications.index') }}"
               class="block text-center px-4 py-2 text-sm text-indigo-400 hover:text-indigo-300 border-t border-slate-700">
                عرض كل الإشعارات
            </a>
        </x-slot>
    </x-dropdown>
</div>