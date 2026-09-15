<?php

/*
|--------------------------------------------------------------------------
| Games catalog (starting with Clash of Clans)
|--------------------------------------------------------------------------
|
| Prices are stored in USD. The game page converts them to SDG with the
| site-wide exchange rate (settings table -> config/store.php -> env).
| Later this can move to the database with a Filament admin UI.
|
*/
return [
    'clash-of-clans' => [
        'slug' => 'clash-of-clans',
        'name' => 'كلاش أوف كلانس',
        'image' => 'images/games/clash of clans.jpg',
        'description' => 'شحن الجواهر والعروض الخاصة بكلاش أوف كلانس.',
        'offers' => [
            ['id' => 'gems-50', 'label' => '50 gems', 'price_usd' => 4.99],
            ['id' => 'gems-200', 'label' => '200 gems', 'price_usd' => 14.99],
            ['id' => 'gems-600', 'label' => '600 gems', 'price_usd' => 29.99],
            ['id' => 'super-pass', 'label' => 'superbass card', 'price_usd' => 9.99],
        ],
    ],
];
