<x-layout>
    <div class="max-w-3xl mx-auto space-y-6">
        <h1 class="text-3xl font-extrabold text-white">الإشعارات</h1>

        <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 sm:p-8">
            @if ($notifications->isEmpty())
                <p class="text-slate-400 text-sm">لا توجد إشعارات حالياً.</p>
            @else
                <div class="space-y-3"
                     x-data="{
                         notifications: @js($notifications->map(fn ($n) => [
                             'id' => $n->id,
                             'read' => ! is_null($n->read_at),
                         ])),
                         isRead(id) {
                             const found = this.notifications.find(n => n.id === id);
                             return found ? found.read : false;
                         },
                         markAsRead(id) {
                             const found = this.notifications.find(n => n.id === id);
                             if (! found || found.read) return;
                             axios.post('{{ url('notifications') }}/' + id + '/read')
                                 .then(() => { found.read = true; })
                                 .catch(() => {});
                         }
                     }">
                    @foreach ($notifications as $notification)
                        <button type="button"
                            @click="markAsRead('{{ $notification->id }}')"
                            class="w-full text-start border rounded-lg p-4 transition"
                            :class="isRead('{{ $notification->id }}') ? 'border-slate-700 text-slate-400' : 'border-indigo-500/50 bg-indigo-500/5 text-white'">
                            <p class="font-semibold">{{ $notification->data['title'] ?? 'إشعار' }}</p>
                            @if (!empty($notification->data['body']))
                                <p class="text-sm mt-1 text-slate-400">{{ $notification->data['body'] }}</p>
                            @endif
                            <p class="text-xs text-slate-500 mt-2">{{ $notification->created_at->diffForHumans() }}</p>
                        </button>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layout>