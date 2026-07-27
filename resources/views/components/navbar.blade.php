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
                <span class="px-3 py-2 rounded-md text-slate-300">{{ auth()->user()->name }}</span>
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
            <div class="block px-3 py-2 rounded-md text-slate-300 bg-slate-900 border border-slate-700">
                👤 {{ auth()->user()->name }}
            </div>
        @else
            <a href="{{ route('login') }}" class="block text-center px-3 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">تسجيل الدخول</a>
        @endauth
    </div>
</header>