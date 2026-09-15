<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    public function home()
    {
        // DB games are APPENDED to the hardcoded cards, grouped by section.
        // Slugs already covered by a hardcoded card (currently everything in
        // config/games.php) are excluded to avoid duplicates. When the
        // hardcoded cards are removed later, those rows appear automatically.
        $hardcodedSlugs = array_keys(config('games', []));

        // Guarded for fresh/test databases where migrations haven't run:
        // homepage must render with hardcoded cards only.
        $dbGames = collect();
        if (\Illuminate\Support\Facades\Schema::hasTable('games')) {
            $dbGames = \App\Models\Game::with('activeServices')
                ->where('is_active', true)
                ->whereNotIn('slug', $hardcodedSlugs)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
                ->groupBy('group');
        }

        return view('pages.home', ['dbGames' => $dbGames]);
    }

    public function about()
    {
        return view('pages.about');
    }

    public function contact()
    {
        return view('pages.contact');
    }
}

