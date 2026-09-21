<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class GameController extends Controller
{
    /**
     * Game detail page: same pic as the home card (bigger, right-hand),
     * game name, offers grid, and a selection box with the SDG total.
     *
     * Catalog is DB-driven (admin-managed via Filament). Unknown slugs and
     * inactive games 404. Inactive services are hidden via activeServices.
     */
    public function show(string $slug): View
    {
        // Guarded for databases where migrations haven't run yet.
        $dbGame = null;
        if (Schema::hasTable('games')) {
            $dbGame = Game::with('activeServices')->where('slug', $slug)->first();
        }

        abort_unless($dbGame !== null && $dbGame->is_active, 404);

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
}
