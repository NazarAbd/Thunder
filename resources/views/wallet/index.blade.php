<x-layout>
    <style>
        [id^="topup-"]:target {
            outline: 2px solid #6366f1;
            outline-offset: 2px;
            border-radius: 0.5rem;
            animation: topup-highlight-fade 2.5s ease-out;
        }

        @keyframes topup-highlight-fade {
            0% {
                background-color: rgba(99, 102, 241, 0.18);
            }
            100% {
                background-color: transparent;
            }
        }
    </style>

    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-extrabold text-white">محفظتي</h1>
            <a href="{{ route('wallet.topup.create') }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-5 py-2 rounded-md transition">
                <i class="fa-solid fa-plus ms-1"></i> طلب شحن رصيد
            </a>
        </div>

        @if (session('status'))
            <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 rounded-lg p-4 text-sm">
                {{ session('status') }}
            </div>
        @endif

        <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 sm:p-10">
            <p class="text-slate-400 text-sm mb-1">الرصيد الحالي</p>
            <p class="text-4xl font-extrabold text-white">{{ number_format($wallet->balance, 2) }} <span class="text-lg text-indigo-400">ج.س</span></p>
        </div>

        <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 sm:p-10">
            <h2 class="text-xl font-bold text-white mb-4">طلبات الشحن السابقة</h2>

            @if ($topUpRequests->isEmpty())
                <p class="text-slate-400 text-sm">لا توجد طلبات شحن حتى الآن.</p>
            @else
                <div class="space-y-3">
                    @foreach ($topUpRequests as $request)
                        <div id="topup-{{ $request->id }}" class="flex items-center justify-between border border-slate-700 rounded-lg p-4">
                            <div>
                                <p class="text-white font-semibold">{{ number_format($request->amount, 2) }} ج.س</p>
                                <p class="text-slate-500 text-xs mt-1">{{ $request->created_at->format('Y-m-d H:i') }}</p>
                                @if ($request->status === \App\Enums\TopUpRequestStatus::Rejected && $request->rejection_reason)
                                    <p class="text-rose-400 text-xs mt-1">سبب الرفض: {{ $request->rejection_reason }}</p>
                                @endif
                            </div>

                            @php
                                $statusClasses = match ($request->status) {
                                    \App\Enums\TopUpRequestStatus::Pending => 'bg-amber-500/10 text-amber-400',
                                    \App\Enums\TopUpRequestStatus::Approved => 'bg-emerald-500/10 text-emerald-400',
                                    \App\Enums\TopUpRequestStatus::Rejected => 'bg-rose-500/10 text-rose-400',
                                };
                            @endphp
                            <span class="text-xs font-semibold px-3 py-1 rounded-full {{ $statusClasses }}">
                                {{ $request->status->label() }}
                            </span>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $topUpRequests->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layout>