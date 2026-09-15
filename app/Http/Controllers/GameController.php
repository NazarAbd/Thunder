<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\View\View;

class GameController extends Controller
{
    /**
     * Game detail page: same pic as the home card (bigger, right-hand),
     * game name, offers grid, and a selection box with the SDG total.
     */
    public function show(string $slug): View
    {
        $rate = Setting::exchangeRate();

        // DB first (admin-managed). Inactive games behave as missing.
        // Guarded for databases where migrations haven't run yet.
        $dbGame = null;
        if (\Illuminate\Support\Facades\Schema::hasTable('games')) {
            $dbGame = \App\Models\Game::with('activeServices')->where('slug', $slug)->first();
        }

        if ($dbGame !== null) {
            abort_unless($dbGame->is_active, 404);

            $offers = $dbGame->activeServices->map(fn ($s): array => [
                'id' => (string) $s->id,
                'label' => $s->label,
                'price_usd' => (float) $s->price_usd,
                'price_sdg' => $s->priceSdg(),
            ])->all();

            return view('games.show', [
                'game' => [
                    'name' => $dbGame->name,
                    'description' => $dbGame->description,
                    'image' => $dbGame->image_url,
                    'offers' => $offers,
                ],
                'slug' => $slug,
            ]);
        }

        // Fallback: legacy hardcoded catalog (config/games.php) until the
        // hardcoded homepage cards — and this fallback — are removed.
        $games = config('games', []);

        abort_unless(isset($games[$slug]), 404);

        $game = $games[$slug];

        $offers = collect($game['offers'] ?? [])->map(fn (array $offer): array => [
            ...$offer,
            'price_sdg' => (int) round((float) $offer['price_usd'] * $rate),
        ])->all();

        return view('games.show', [
            'game' => [...$game, 'image' => asset($game['image']), 'offers' => $offers],
            'slug' => $slug,
        ]);
    }
}
