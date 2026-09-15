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
            'time' => $n->created_at?->diffForHumans(),
            'read' => ! is_null($n->read_at),
        ])),
        unreadCount: {{ $unreadCount }},
        listening: false,
        readUrl(id) {
            return '{{ url('notifications') }}/' + id + '/read';
        },
        markAsRead(notification) {
            if (! notification) return;
            // Server marks read idempotently and returns the deep-link target,
            // so this works for both unread and already-read items.
            axios.post(this.readUrl(notification.id))
                .then((response) => {
                    if (! notification.read) {
                        notification.read = true;
                        if (this.unreadCount > 0) this.unreadCount--;
                    }
                    const redirect = response?.data?.redirect;
                    if (redirect) window.location.href = redirect;
                })
                .catch(() => {});
        },
        markAllRead() {
            axios.post('{{ route('notifications.readAll') }}')
                .then(() => {
                    this.notifications.forEach(n => n.read = true);
                    this.unreadCount = 0;
                })
                .catch(() => {});
        },
        clearAll() {
            // Manual only: removes items from the dropdown (deletes rows).
            // Auto-open of wallet marks as read but never deletes.
            axios.delete('{{ route('notifications.clear') }}')
                .then(() => {
                    this.notifications = [];
                    this.unreadCount = 0;
                })
                .catch(() => {});
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
                        time: 'الآن',
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
            <div class="flex items-center justify-between px-4 py-2 border-b border-slate-700">
                <p class="text-sm font-bold text-white">الإشعارات
                    <span x-show="unreadCount > 0" class="ms-1 text-[10px] bg-indigo-500/20 text-indigo-300 rounded-full px-2 py-0.5" x-text="unreadCount"></span>
                </p>
                <div class="flex items-center gap-3">
                    <button type="button" @click="markAllRead()"
                        class="text-[11px] text-indigo-400 hover:text-indigo-300">
                        تحديد الكل كمقروء
                    </button>
                    <button type="button" @click="clearAll()"
                        class="text-[11px] text-rose-400 hover:text-rose-300">
                        مسح
                    </button>
                </div>
            </div>

            <template x-for="notification in notifications" :key="notification.id">
                <button type="button"
                    @click="markAsRead(notification)"
                    class="w-full text-start px-4 py-3 text-sm transition border-b border-slate-700/50 last:border-0 hover:bg-slate-700"
                    :class="notification.read ? 'text-slate-400' : 'text-white bg-slate-700/40'">
                    <span class="flex items-start gap-2">
                        <span class="mt-1.5 w-2 h-2 rounded-full shrink-0"
                            :class="notification.read ? 'bg-slate-600' : 'bg-indigo-400'"></span>
                        <span class="flex-1">
                            <span class="font-semibold block" x-text="notification.title"></span>
                            <span class="text-xs mt-1 text-slate-400 block whitespace-normal" x-show="notification.body" x-text="notification.body"></span>
                            <span class="text-[11px] mt-1 text-slate-500 block" x-show="notification.time" x-text="notification.time"></span>
                        </span>
                    </span>
                </button>
            </template>

            <p class="px-4 py-3 text-sm text-slate-500" x-show="notifications.length === 0">لا توجد إشعارات</p>

            <a href="{{ route('notifications.index') }}"
               class="block text-center px-4 py-2 text-sm text-indigo-400 hover:text-indigo-300 border-t border-slate-700">
                عرض كل الإشعارات
            </a>
        </x-slot>
    </x-dropdown>
</div>
