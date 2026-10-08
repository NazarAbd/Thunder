<?php

use App\Models\Game;
use Illuminate\Database\Migrations\Migration;

/**
 * Seed the remaining storefront games as admin-editable rows.
 *
 * These 11 games previously existed only as hardcoded placeholder cards on
 * the homepage with dead `/#` links. They are now real catalog rows, so
 * every card links to its `/games/{slug}` detail page like Clash of Clans
 * (seeded in 2026_09_21_000005).
 *
 * Intentionally left empty for the owner to complete via Filament
 * (الألعاب والخدمات):
 *  - `image_path` is null: the card renders its `alt` (game name) until an
 *    image is uploaded.
 *  - No services: the detail page shows an empty offers grid until offers
 *    with real USD prices are added. No fake prices are seeded on purpose.
 *
 * Idempotent: updateOrCreate keyed on slug. Never touches clash-of-clans.
 */
return new class extends Migration
{
    public function up(): void
    {
        $games = [
            // 1. الشحن المباشر
            ['slug' => 'pubg-mobile', 'name' => 'ببجي موبايل', 'description' => 'شحن شدات ببجي موبايل مباشرة عبر معرف اللاعب.', 'group' => 'direct', 'sort_order' => 1],
            ['slug' => 'free-fire', 'name' => 'فري فاير', 'description' => 'شحن جواهر فري فاير مباشرة وبأسعار منافسة.', 'group' => 'direct', 'sort_order' => 2],
            ['slug' => 'mobile-legends', 'name' => 'موبايل ليجيندز', 'description' => 'شحن جواهر موبايل ليجيندز بشكل فوري وآمن.', 'group' => 'direct', 'sort_order' => 3],
            // 2. الشحن بالحساب
            ['slug' => 'valorant', 'name' => 'فالورانت', 'description' => 'شحن نقاط فالورانت مباشرة عبر حسابك.', 'group' => 'account', 'sort_order' => 0],
            ['slug' => 'league-of-legends', 'name' => 'ليج أوف ليجيندز', 'description' => 'شحن RP للعبة ليج أوف ليجيندز عبر تسجيل الدخول.', 'group' => 'account', 'sort_order' => 1],
            ['slug' => 'roblox', 'name' => 'روبلوکس', 'description' => 'شحن Robux مباشرة عبر حسابك الشخصي.', 'group' => 'account', 'sort_order' => 2],
            ['slug' => 'minecraft', 'name' => 'ماين كرافت', 'description' => 'شحن عملات ماين كرافت وتفعيل الاشتراكات.', 'group' => 'account', 'sort_order' => 3],
            // 3. الاشتراكات الرقمية
            ['slug' => 'chatgpt-plus', 'name' => 'ChatGPT Plus', 'description' => 'اشتراك شهري في ChatGPT Plus بأحدث النماذج.', 'group' => 'subscriptions', 'sort_order' => 0],
            ['slug' => 'gemini-advanced', 'name' => 'Gemini Advanced', 'description' => 'اشتراك Gemini Advanced بميزات الذكاء الاصطناعي المتقدمة.', 'group' => 'subscriptions', 'sort_order' => 1],
            ['slug' => 'netflix', 'name' => 'نتفليكس (Netflix)', 'description' => 'اشتراكات شهرية وسنوية بأعلى جودة 4K Ultra HD.', 'group' => 'subscriptions', 'sort_order' => 2],
            ['slug' => 'spotify', 'name' => 'سبوتيفاي (Spotify)', 'description' => 'اشتراك Spotify Premium بدون إعلانات.', 'group' => 'subscriptions', 'sort_order' => 3],
        ];

        foreach ($games as $data) {
            Game::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'image_path' => null,
                    'group' => $data['group'],
                    'sort_order' => $data['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }

    public function down(): void
    {
        Game::whereIn('slug', [
            'pubg-mobile',
            'free-fire',
            'mobile-legends',
            'valorant',
            'league-of-legends',
            'roblox',
            'minecraft',
            'chatgpt-plus',
            'gemini-advanced',
            'netflix',
            'spotify',
        ])->delete();
    }
};
