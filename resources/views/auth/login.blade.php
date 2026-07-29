<x-layout>
    <div class="flex items-center justify-center min-h-[50vh]">
        <div class="w-full max-w-md">
            <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 sm:p-8">
                <div class="border-b border-slate-700 pb-3 mb-4 text-center">
                    <h1 class="text-2xl font-extrabold text-white">تسجيل الدخول</h1>
                     {{-- <p class="text-indigo-400 mt-1 text-sm">مرحباً بعودتك! سجّل الدخول للمتابعة</p> --}}
                </div>

                @if (session('status'))
                    <div class="mb-3 font-medium text-sm text-emerald-400 text-center">
                        {{ session('status') }}
                    </div>
                @endif

                <!-- Google login -->
                <a href="{{ route('auth.google.redirect') }}"
                   class="flex items-center justify-center gap-3 w-full bg-white text-slate-700 font-semibold py-2 rounded-md border border-slate-300 hover:bg-gray-100 transition">
                    <span class="w-5 h-5 shrink-0 flex items-center justify-center">
                        <svg class="w-5 h-5 block" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                            <path fill="#FFC107" d="M43.611 20.083H42V20H24v8h11.303c-1.649 4.657-6.08 8-11.303 8-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z"/>
                            <path fill="#FF3D00" d="M6.306 14.691l6.571 4.819C14.655 15.108 18.961 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 16.318 4 9.656 8.337 6.306 14.691z"/>
                            <path fill="#4CAF50" d="M24 44c5.166 0 9.86-1.977 13.409-5.192l-6.19-5.238C29.211 35.091 26.715 36 24 36c-5.202 0-9.619-3.317-11.283-7.946l-6.522 5.025C9.505 39.556 16.227 44 24 44z"/>
                            <path fill="#1976D2" d="M43.611 20.083H42V20H24v8h11.303a12.04 12.04 0 0 1-4.087 5.571l.003-.002 6.19 5.238C36.971 39.205 44 34 44 24c0-1.341-.138-2.65-.389-3.917z"/>
                        </svg>
                    </span>
                    <span>الدخول عبر جوجل</span>
                </a>

                <div class="flex items-center gap-3 my-4">
                    <div class="flex-1 h-px bg-slate-700"></div>
                    <span class="text-slate-500 text-xs">أو</span>
                    <div class="flex-1 h-px bg-slate-700"></div>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="email" class="block font-medium text-sm text-slate-300">البريد الإلكتروني</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                            class="mt-1 block w-full rounded-md bg-slate-900 border-slate-700 text-slate-100 placeholder-slate-500 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        <x-input-error :messages="$errors->get('email')" class="mt-1 !text-red-400" />
                    </div>

                    <div>
                        <label for="password" class="block font-medium text-sm text-slate-300">كلمة المرور</label>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                            class="mt-1 block w-full rounded-md bg-slate-900 border-slate-700 text-slate-100 placeholder-slate-500 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        <x-input-error :messages="$errors->get('password')" class="mt-1 !text-red-400" />
                    </div>

                    <div class="flex items-center justify-between">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox" name="remember"
                                class="rounded border-slate-600 bg-slate-900 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <span class="ms-2 text-sm text-slate-400">تذكرني</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="text-sm text-indigo-400 hover:text-indigo-300 underline" href="{{ route('password.request') }}">
                                نسيت كلمة المرور؟
                            </a>
                        @endif
                    </div>

                    <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 rounded-md transition">
                        تسجيل الدخول
                    </button>
                </form>

                <p class="text-center text-slate-400 text-sm mt-2">
                    ليس لديك حساب؟
                    <a href="{{ route('register') }}" class="text-indigo-400 hover:text-indigo-300 underline">إنشاء حساب</a>
                </p>
            </div>
        </div>
    </div>
</x-layout>