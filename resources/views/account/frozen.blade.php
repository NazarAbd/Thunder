<x-layout title="الحساب موقوف | البـرق">
    <div class="max-w-xl mx-auto">
        <div class="bg-slate-800 border border-red-500/40 rounded-2xl p-6 sm:p-10 text-center space-y-4">
            <div class="w-14 h-14 mx-auto rounded-full bg-red-500/10 text-red-400 flex items-center justify-center text-2xl">
                <i class="fa-solid fa-ban"></i>
            </div>
            <h1 class="text-2xl font-extrabold text-white">حسابك موقوف مؤقتاً</h1>
            <p class="text-slate-300 text-sm leading-7">
                تم تجميد محفظتك ولا يمكنك إجراء أي عملية حالياً.
                لفك التجميد تواصل مع الإدارة عبر واتساب واذكر بريدك المسجل.
            </p>
            @php($wa = config('store.whatsapp'))
            @if(!empty($wa))
                <a href="https://wa.me/{{ $wa }}?text={{ urlencode('مرحباً، حسابي موقوف: ' . auth()->user()->email) }}"
                   target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white font-bold px-6 py-3 rounded-xl transition">
                    <i class="fa-brands fa-whatsapp text-xl"></i>
                    التواصل عبر واتساب
                </a>
            @else
                <a href="{{ route('contact') }}"
                   class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-3 rounded-xl transition">
                    الذهاب لصفحة اتصل بنا
                </a>
            @endif
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="text-slate-400 hover:text-white text-sm underline underline-offset-4">
                    تسجيل الخروج
                </button>
            </form>
        </div>
    </div>
</x-layout>
