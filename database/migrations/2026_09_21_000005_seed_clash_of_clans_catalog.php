<?php

use App\Models\Game;
use Illuminate\Database\Migrations\Migration;

/**
 * Seed the Clash of Clans catalog into the database.
 *
 * The storefront is DB-driven (see GameController/PageController): games and
 * their services are admin-managed rows. This migration preserves the exact
 * catalog previously hardcoded in config/games.php (now deleted) so fresh
 * installs, tests (:memory:), and existing databases all serve
 * /games/clash-of-clans identically with zero code fallback.
 *
 * Idempotent: uses updateOrCreate keyed on slug (game) and game_id + label
 * (services), so re-running never duplicates the existing row (id 1).
 */
return new class extends Migration
{
    public function up(): void
    {
        $game = Game::updateOrCreate(
            ['slug' => 'clash-of-clans'],
            [
                'name' => 'كلاش أوف كلانس',
                'description' => 'شحن الجواهر والعروض الخاصة بكلاش أوف كلانس.',
                'image_path' => 'images/games/clash of clans.jpg',
                'group' => 'direct',
                'sort_order' => 0,
                'is_active' => true,
            ]
        );

        $offers = [
            ['label' => '50 gems', 'price_usd' => '4.99', 'sort_order' => 0],
            ['label' => '200 gems', 'price_usd' => '14.99', 'sort_order' => 1],
            ['label' => '600 gems', 'price_usd' => '29.99', 'sort_order' => 2],
            ['label' => 'superbass card', 'price_usd' => '9.99', 'sort_order' => 3],
        ];

        foreach ($offers as $offer) {
            $game->services()->updateOrCreate(
                ['label' => $offer['label']],
                [
                    'price_usd' => $offer['price_usd'],
                    'sort_order' => $offer['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }

    public function down(): void
    {
        $game = Game::where('slug', 'clash-of-clans')->first();

        if ($game !== null) {
            $game->services()->delete();
            $game->delete();
        }
    }
};
