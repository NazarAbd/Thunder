<x-layout :title="$game['name'] . ' | البـرق'">
    <div class="space-y-6"
        x-data="{
            offers: @js($game['offers']),
            selectedId: null,
            selected() { return this.offers.find(o => o.id === this.selectedId); },
            select(id) { this.selectedId = id; },
            sdg(n) { return new Intl.NumberFormat('en-US').format(n) + ' SDG'; }
        }">

        {{-- Breadcrumb: where the user currently is --}}
        <nav class="flex items-center gap-2 text-sm text-slate-400" aria-label="breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-white transition">games</a>
            <span class="text-slate-600">&gt;</span>
            <a href="{{ route('games.show', $slug) }}" class="hover:text-white transition">{{ $slug }}</a>
            <span class="text-slate-600">&gt;</span>
        </nav>

        {{-- Header: same pic as home card but bigger, right-hand + game name --}}
        <section class="bg-slate-800 border border-slate-700 rounded-2xl p-6 sm:p-8">
            <div class="flex items-center gap-5 sm:gap-8">
                <img src="{{ $game['image'] }}" alt="{{ $game['name'] }}"
                    class="w-28 h-28 sm:w-40 sm:h-40 lg:w-56 lg:h-56 rounded-2xl object-cover border border-slate-700 shrink-0">
                <div class="min-w-0">
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white">{{ $game['name'] }}</h1>
                    @if(!empty($game['description']))
                        <p class="text-slate-400 text-sm sm:text-base mt-2">{{ $game['description'] }}</p>
                    @endif

                </div>
            </div>
        </section>

        {{-- Services: small blocks underneath the pic, full width --}}
        <section class="bg-slate-800 border border-slate-700 rounded-2xl p-6 sm:p-8">
            <h2 class="text-lg font-bold text-white mb-4">العروض المتاحة</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
                <template x-for="offer in offers" :key="offer.id">
                    <button type="button" @click="select(offer.id)"
                        class="border rounded-xl px-4 py-4 text-center transition"
                        :class="selectedId === offer.id
                            ? 'border-indigo-500 bg-indigo-500/10 text-white'
                            : 'border-slate-700 text-slate-200 hover:border-indigo-500/60 hover:bg-slate-700/40'">
                        <span class="block font-bold" x-text="offer.label"></span>
                        <span class="block text-xs text-slate-400 mt-1" x-text="sdg(offer.price_sdg)"></span>
                    </button>
                </template>
            </div>
        </section>

        {{-- Selection box underneath everything --}}
        <section class="bg-slate-800 border border-indigo-500/40 rounded-2xl p-6 sm:p-8">
            <template x-if="selected()">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <p class="text-white">
                        <span class="text-slate-400 text-sm">اخترت:</span>
                        <span class="font-bold" x-text="selected().label"></span>
                    </p>
                    <p class="text-xl font-extrabold text-indigo-400" x-text="sdg(selected().price_sdg)"></p>
                </div>
            </template>
            <template x-if="! selected()">
                <p class="text-slate-400 text-sm">اختر عرضاً من الأعلى لعرض السعر بالجنيه السوداني هنا.</p>
            </template>
        </section>
    </div>
</x-layout>
