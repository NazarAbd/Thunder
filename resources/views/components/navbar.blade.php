<!-- resources/views/components/navbar.blade.php -->
{{-- <header x-data="{ open: false }" class="bg-slate-800 border-b border-slate-700 sticky top-0 z-50"> --}}
    <header x-data="{ open: false }"
    class="relative bg-slate-800 border-b border-slate-700 top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center h-16">

        <a href="{{ route('home') }}" class="flex items-center gap-3">
            <div class="bg-indigo-600 text-white p-2 rounded-lg text-xl">
                <i class="fa-solid fa-bolt"></i>
            </div>
            <span class="font-extrabold text-xl tracking-wide text-white">البـرق</span>
        </a>

        <nav class="hidden md:flex items-center gap-1 sm:gap-2 text-sm font-medium">
            <a href="{{ route('home') }}" class="px-3 py-2 rounded-md {{ request()->routeIs('home') ? 'text-indigo-400 bg-slate-900' : 'text-slate-300 hover:text-white hover:bg-slate-700' }}">الرئيسية</a>
            <a href="{{ route('about') }}" class="px-3 py-2 rounded-md {{ request()->routeIs('about') ? 'text-indigo-400 bg-slate-900' : 'text-slate-300 hover:text-white hover:bg-slate-700' }}">من نحن</a>
            <a href="{{ route('contact') }}" class="px-3 py-2 rounded-md {{ request()->routeIs('contact') ? 'text-indigo-400 bg-slate-900' : 'text-slate-300 hover:text-white hover:bg-slate-700' }}">اتصل بنا</a>

            @auth
                <x-dropdown align="right" width="48" content-classes="py-1 bg-slate-800 border border-slate-700">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-1 px-3 py-1.5 rounded-md text-slate-300 hover:text-white hover:bg-slate-700 focus:outline-none transition text-sm">
                            <span>{{ auth()->user()->name }}</span>
                            <svg class="fill-current h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-3 py-1.5 text-sm text-slate-300 hover:text-white hover:bg-slate-700 transition">
                            <i class="fa-solid fa-user w-4 text-center text-slate-500"></i>
                            <span>الملف الشخصي</span>
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}"
                                onclick="event.preventDefault(); this.closest('form').submit();"
                                class="flex items-center gap-2 px-3 py-1.5 text-sm text-slate-300 hover:text-white hover:bg-slate-700 transition cursor-pointer">
                                <i class="fa-solid fa-right-from-bracket w-4 text-center text-slate-500"></i>
                                <span>تسجيل الخروج</span>
                            </a>
                        </form>
                    </x-slot>
                </x-dropdown>
            @else
                <a href="{{ route('login') }}" class="px-3 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">تسجيل الدخول</a>
            @endauth
        </nav>

        <div class="flex items-center md:hidden">
            <button @click="open = ! open" class="text-slate-300 hover:text-white p-2 rounded-md hover:bg-slate-700 focus:outline-none">
                <svg class="h-6 w-6" :class="{'hidden': open, 'block': ! open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg class="h-6 w-6" :class="{'block': open, 'hidden': ! open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    {{-- <div :class="{'block': open, 'hidden': ! open}" class="hidden md:hidden bg-slate-800 border-b border-slate-700 px-4 pt-2 pb-4 space-y-2 text-sm font-medium"> --}}
        <div
    x-show="open"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 -translate-y-2"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 -translate-y-2"
    @click.away="open = false"
    class="absolute top-full left-0 right-0 md:hidden bg-slate-800 border-b border-slate-700 px-4 pt-2 pb-4 space-y-2 text-sm font-medium shadow-xl z-[100]"
    x-cloak
>
        <a href="{{ route('home') }}" class="block px-3 py-2 rounded-md {{ request()->routeIs('home') ? 'text-indigo-400 bg-slate-900' : 'text-slate-300 hover:text-white hover:bg-slate-700' }}">الرئيسية</a>
        <a href="{{ route('about') }}" class="block px-3 py-2 rounded-md {{ request()->routeIs('about') ? 'text-indigo-400 bg-slate-900' : 'text-slate-300 hover:text-white hover:bg-slate-700' }}">من نحن</a>
        <a href="{{ route('contact') }}" class="block px-3 py-2 rounded-md {{ request()->routeIs('contact') ? 'text-indigo-400 bg-slate-900' : 'text-slate-300 hover:text-white hover:bg-slate-700' }}">اتصل بنا</a>

        @auth
            <div class="px-3 py-2 rounded-md text-slate-300 bg-slate-900 border border-slate-700 space-y-1">
                <div class="flex items-center gap-2 px-2 py-2 text-slate-200">
                    <i class="fa-solid fa-circle-user text-lg"></i>
                    <span class="font-medium">{{ auth()->user()->name }}</span>
                </div>

                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-2 py-2 rounded-md text-slate-300 hover:text-white hover:bg-slate-700">
                    <i class="fa-solid fa-user w-4 text-center"></i>
                    <span>الملف الشخصي</span>
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}"
                        onclick="event.preventDefault(); this.closest('form').submit();"
                        class="flex items-center gap-2 px-2 py-2 rounded-md text-slate-300 hover:text-white hover:bg-slate-700 cursor-pointer">
                        <i class="fa-solid fa-right-from-bracket w-4 text-center"></i>
                        <span>تسجيل الخروج</span>
                    </a>
                </form>
            </div>
        @else
            <a href="{{ route('login') }}" class="block text-center px-3 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">تسجيل الدخول</a>
        @endauth
    </div>
</header>