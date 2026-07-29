<x-layout>
    <div class="flex items-center justify-center min-h-[55vh]">
        <div class="w-full max-w-md">
            <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 sm:p-8">
                <div class="border-b border-slate-700 pb-3 mb-4 text-center">
                    <h1 class="text-2xl font-extrabold text-white">نسيت كلمة المرور؟</h1>
                    <p class="text-indigo-400 mt-1 text-sm">لا مشكلة. أدخل بريدك الإلكتروني وسنرسل لك رابط إعادة التعيين</p>
                </div>

                @if (session('status'))
                    <div class="mb-3 font-medium text-sm text-emerald-400 text-center">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="email" class="block font-medium text-sm text-slate-300">البريد الإلكتروني</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="mt-1 block w-full rounded-md bg-slate-900 border-slate-700 text-slate-100 placeholder-slate-500 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        <x-input-error :messages="$errors->get('email')" class="mt-1 !text-red-400" />
                    </div>

                    <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 rounded-md transition">
                        إرسال رابط إعادة التعيين
                    </button>
                </form>

                <p class="text-center text-slate-400 text-sm mt-4">
                    تذكرت كلمة المرور؟
                    <a href="{{ route('login') }}" class="text-indigo-400 hover:text-indigo-300 underline">تسجيل الدخول</a>
                </p>
            </div>
        </div>
    </div>
</x-layout>