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
                <x-game-card
                    name="ببجي موبايل"
                    description="شحن شدات ببجي موبايل مباشرة عبر معرف اللاعب."
                    image="https://placehold.co/500x300/1e293b/ffffff?text=PUBG"
                />
                <x-game-card
                    name="فري فاير"
                    description="شحن جواهر فري فاير مباشرة وبأسعار منافسة."
                    image="https://placehold.co/500x300/1e293b/ffffff?text=Free+Fire"
                />
                <x-game-card
                    name="موبايل ليجيندز"
                    description="شحن جواهر موبايل ليجيندز بشكل فوري وآمن."
                    image="https://placehold.co/500x300/1e293b/ffffff?text=Mobile+Legends"
                />
                {{-- Admin-managed games (DB): every active `direct` game, incl. Clash of Clans. --}}
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
                <x-game-card
                    name="فالورانت"
                    description="شحن نقاط فالورانت مباشرة عبر حسابك."
                    image="https://placehold.co/500x300/1e293b/ffffff?text=Valorant"
                />
                <x-game-card
                    name="ليج أوف ليجيندز"
                    description="شحن RP للعبة ليج أوف ليجيندز عبر تسجيل الدخول."
                    image="https://placehold.co/500x300/1e293b/ffffff?text=LoL"
                />
                <x-game-card
                    name="روبلوکس"
                    description="شحن Robux مباشرة عبر حسابك الشخصي."
                    image="https://placehold.co/500x300/1e293b/ffffff?text=Roblox"
                />
                <x-game-card
                    name="ماين كرافت"
                    description="شحن عملات ماين كرافت وتفعيل الاشتراكات."
                    image="https://placehold.co/500x300/1e293b/ffffff?text=Minecraft"
                />
                {{-- Admin-added games (DB). --}}
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
                <x-game-card
                    name="ChatGPT Plus"
                    description="اشتراك شهري في ChatGPT Plus بأحدث النماذج."
                    image="https://placehold.co/500x300/1e293b/ffffff?text=ChatGPT"
                />
                <x-game-card
                    name="Gemini Advanced"
                    description="اشتراك Gemini Advanced بميزات الذكاء الاصطناعي المتقدمة."
                    image="https://placehold.co/500x300/1e293b/ffffff?text=Gemini"
                />
                <x-game-card
                    name="نتفليكس (Netflix)"
                    description="اشتراكات شهرية وسنوية بأعلى جودة 4K Ultra HD."
                    image="https://placehold.co/500x300/1e293b/ffffff?text=Netflix"
                />
                <x-game-card
                    name="سبوتيفاي (Spotify)"
                    description="اشتراك Spotify Premium بدون إعلانات."
                    image="https://placehold.co/500x300/1e293b/ffffff?text=Spotify"
                />
                {{-- Admin-added games (DB). --}}
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