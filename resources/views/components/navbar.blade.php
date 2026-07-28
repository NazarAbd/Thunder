<header x-data="{ open: false }" class="bg-slate-800 border-b border-slate-700 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center h-16">

        <!-- اللوجو واسم المتجر -->
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            <div class="bg-indigo-600 text-white p-2 rounded-lg text-xl">
                <i class="fa-solid fa-bolt"></i>
            </div>
            <span class="font-extrabold text-xl tracking-wide text-white">البـرق</span>
        </a>

        <!-- القائمة للشاشات الكبيرة (تختفي في الموبايل md:flex) -->
        <nav class="hidden md:flex items-center gap-1 sm:gap-2 text-sm font-medium">
            <a href="{{ route('home') }}" class="px-3 py-2 rounded-md {{ request()->routeIs('home') ? 'text-indigo-400 bg-slate-900' : 'text-slate-300 hover:text-white hover:bg-slate-700' }}">الرئيسية</a>
            <a href="{{ route('about') }}" class="px-3 py-2 rounded-md {{ request()->routeIs('about') ? 'text-indigo-400 bg-slate-900' : 'text-slate-300 hover:text-white hover:bg-slate-700' }}">من نحن</a>
            <a href="{{ route('contact') }}" class="px-3 py-2 rounded-md {{ request()->routeIs('contact') ? 'text-indigo-400 bg-slate-900' : 'text-slate-300 hover:text-white hover:bg-slate-700' }}">اتصل بنا</a>

            @auth
                <x-dropdown align="right" width="48" content-classes="py-1 bg-white border border-gray-200">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-1 px-3 py-2 rounded-md text-slate-300 hover:text-white hover:bg-slate-700 focus:outline-none transition">
                            <span>{{ auth()->user()->name }}</span>
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            <span class="inline-flex items-center gap-2">
                                <i class="fa-solid fa-user w-4 text-center text-slate-500"></i>
                                <span>الملف الشخصي</span>
                            </span>
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                <span class="inline-flex items-center gap-2">
                                    <i class="fa-solid fa-right-from-bracket w-4 text-center text-slate-500"></i>
                                    <span>تسجيل الخروج</span>
                                </span>
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            @else
                <a href="{{ route('login') }}" class="px-3 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">تسجيل الدخول</a>
            @endauth
        </nav>

        <!-- زر الهامبرغر (يظهر في الموبايل فقط md:hidden) -->
        <div class="flex items-center md:hidden">
            <button @click="open = ! open" class="text-slate-300 hover:text-white p-2 rounded-md hover:bg-slate-700 focus:outline-none">
                <!-- أيقونة فتح القائمة -->
                <svg class="h-6 w-6" :class="{'hidden': open, 'block': ! open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <!-- أيقونة إغلاق القائمة (X) -->
                <svg class="h-6 w-6" :class="{'block': open, 'hidden': ! open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

    </div>

    <!-- قائمة الموبايل المنسدلة (تظهر عند الضغط على الزر) -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden md:hidden bg-slate-800 border-b border-slate-700 px-4 pt-2 pb-4 space-y-2 text-sm font-medium">
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