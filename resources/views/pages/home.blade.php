<x-layout>
    {{-- Welcome banner (kept from the original page) --}}
    <div class="text-center py-12">
        <h1 class="text-3xl font-extrabold text-white">مرحب بك في متجر البرق</h1>
    </div>

    <div class="space-y-8">

        {{-- 1. الشحن المباشر (direct top-up via Player ID) --}}
        <section class="bg-slate-800 border border-slate-700 rounded-2xl p-6 sm:p-10 flex flex-col min-h-[550px]">
            <div class="flex items-center gap-4 border-b border-slate-700 pb-4 mb-6">
                <div class="w-12 h-12 shrink-0 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-2xl">
                    <i class="fa-solid fa-gamepad"></i>
                </div>
                <h1 class="text-2xl font-extrabold text-white">1- الشحن المباشر</h1>
            </div>

            <div class="flex-1 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                {{-- Admin-managed games (DB). Cards without an uploaded image
                     render their name (alt) until the owner adds a picture. --}}
                @foreach(($dbGames['direct'] ?? collect()) as $dbGame)
                    <x-game-card
                        :name="$dbGame->name"
                        :description="$dbGame->description ?? ''"
                        :image="$dbGame->image_url"
                        :href="route('games.show', $dbGame->slug)"
                    />
                @endforeach
            </div>
        </section>

        {{-- 2. الشحن بالحساب (top-up via account login) --}}
        <section class="bg-slate-800 border border-slate-700 rounded-2xl p-6 sm:p-10 flex flex-col min-h-[550px]">
            <div class="flex items-center gap-4 border-b border-slate-700 pb-4 mb-6">
                <div class="w-12 h-12 shrink-0 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-2xl">
                    <i class="fa-solid fa-right-to-bracket"></i>
                </div>
                <h1 class="text-2xl font-extrabold text-white">2- الشحن بالحساب</h1>
            </div>

            <div class="flex-1 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                {{-- Admin-managed games (DB). --}}
                @foreach(($dbGames['account'] ?? collect()) as $dbGame)
                    <x-game-card
                        :name="$dbGame->name"
                        :description="$dbGame->description ?? ''"
                        :image="$dbGame->image_url"
                        :href="route('games.show', $dbGame->slug)"
                    />
                @endforeach
            </div>
        </section>

        {{-- 3. الاشتراكات الرقمية (subscriptions: Gemini, ChatGPT, etc.) --}}
        <section class="bg-slate-800 border border-slate-700 rounded-2xl p-6 sm:p-10 flex flex-col min-h-[550px]">
            <div class="flex items-center gap-4 border-b border-slate-700 pb-4 mb-6">
                <div class="w-12 h-12 shrink-0 rounded-xl bg-amber-500 text-white flex items-center justify-center text-2xl">
                    <i class="fa-solid fa-robot"></i>
                </div>
                <h1 class="text-2xl font-extrabold text-white">3- الاشتراكات الرقمية</h1>
            </div>

            <div class="flex-1 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                {{-- Admin-managed games (DB). --}}
                @foreach(($dbGames['subscriptions'] ?? collect()) as $dbGame)
                    <x-game-card
                        :name="$dbGame->name"
                        :description="$dbGame->description ?? ''"
                        :image="$dbGame->image_url"
                        :href="route('games.show', $dbGame->slug)"
                    />
                @endforeach
            </div>
        </section>

    </div>
</x-layout>