<x-layout>
    <div class="flex items-center justify-center min-h-[55vh]">
        <div class="w-full max-w-md">
            <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 sm:p-8">
                <div class="border-b border-slate-700 pb-3 mb-4 text-center">
                    <h1 class="text-2xl font-extrabold text-white">إنشاء حساب</h1>
                    <p class="text-indigo-400 mt-1 text-sm">أنشئ حسابك للبدء في استخدام المنصة</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="name" class="block font-medium text-sm text-slate-300">الاسم</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                            class="mt-1 block w-full rounded-md bg-slate-900 border-slate-700 text-slate-100 placeholder-slate-500 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        <x-input-error :messages="$errors->get('name')" class="mt-1 !text-red-400" />
                    </div>

                    <div>
                        <label for="email" class="block font-medium text-sm text-slate-300">البريد الإلكتروني</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                            class="mt-1 block w-full rounded-md bg-slate-900 border-slate-700 text-slate-100 placeholder-slate-500 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        <x-input-error :messages="$errors->get('email')" class="mt-1 !text-red-400" />
                    </div>

                    <div>
                        <label for="password" class="block font-medium text-sm text-slate-300">كلمة المرور</label>
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                            class="mt-1 block w-full rounded-md bg-slate-900 border-slate-700 text-slate-100 placeholder-slate-500 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        <x-input-error :messages="$errors->get('password')" class="mt-1 !text-red-400" />
                    </div>

                    <div>
                        <label for="password_confirmation" class="block font-medium text-sm text-slate-300">تأكيد كلمة المرور</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                            class="mt-1 block w-full rounded-md bg-slate-900 border-slate-700 text-slate-100 placeholder-slate-500 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 !text-red-400" />
                    </div>

                    <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 rounded-md transition">
                        إنشاء حساب
                    </button>
                </form>

                <p class="text-center text-slate-400 text-sm mt-4">
                    لديك حساب بالفعل؟
                    <a href="{{ route('login') }}" class="text-indigo-400 hover:text-indigo-300 underline">تسجيل الدخول</a>
                </p>
            </div>
        </div>
    </div>
</x-layout>