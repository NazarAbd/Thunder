<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    public function home()
    {
        // Storefront catalog is DB-driven (admin-managed via Filament).
        // Every active game row appears in its homepage section by `group`.
        // Guarded for fresh/test databases where migrations haven't run:
        // homepage must still render (without game cards).
        $dbGames = collect();
        if (\Illuminate\Support\Facades\Schema::hasTable('games')) {
            $dbGames = \App\Models\Game::with('activeServices')
                ->where('is_active', true)
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

