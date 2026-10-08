<x-layout>
    <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 sm:p-10 max-w-4xl mx-auto">
        <div class="border-b border-slate-700 pb-4 mb-6">
            <h1 class="text-3xl font-extrabold text-white">اتصل بنا</h1>
            <p class="text-indigo-400 mt-1 text-sm">نحن هنا لمساعدتك والإجابة على جميع استفساراتك</p>
        </div>
        <div class="space-y-6">
            <div class="flex items-center gap-3 text-slate-300">
                <div class="w-10 h-10 rounded-lg bg-emerald-500/10 text-emerald-500 flex items-center justify-center">
                    <i class="fa-brands fa-whatsapp text-xl"></i>
                </div>
                <div class="flex-1">
                    <p class="text-xs text-slate-400">واتساب الدعم الفني</p>
                    @php($wa = config('store.whatsapp'))
                    @if(!empty($wa))
                        <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener"
                           class="font-semibold text-emerald-400 hover:text-emerald-300 transition" dir="ltr">+{{ $wa }}</a>
                    @else
                        <p class="font-semibold">+1234567890</p>
                    @endif
                </div>
                @if(!empty($wa))
                    <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener"
                       class="bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-bold px-4 py-2 rounded-lg transition shrink-0">
                        <i class="fa-brands fa-whatsapp"></i> راسلنا
                    </a>
                @endif
            </div>
            <div class="flex items-center gap-3 text-slate-300">
                <div class="w-10 h-10 rounded-lg bg-indigo-500/10 text-indigo-500 flex items-center justify-center">
                    <i class="fa-solid fa-envelope text-xl"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-400">البريد الإلكتروني</p>
                    <p class="font-semibold">{{ config('store.support_email') }}</p>
                </div>
            </div>
        </div>
    </div>
</x-layout>