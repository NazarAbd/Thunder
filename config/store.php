<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Site-wide exchange rate (SDG per 1 USD)
    |--------------------------------------------------------------------------
    |
    | Default used ONLY when no `exchange_rates` table row is active.
    | The admin-managed table (Filament: سعر الصرف) is the live source;
    | this value is the last-resort fallback so the store never renders
    | without a rate. May be overridden via EXCHANGE_RATE_SDG_PER_USD.
    |
    */
    'exchange_rate' => (float) env('EXCHANGE_RATE_SDG_PER_USD', 3000),

    /*
    |--------------------------------------------------------------------------
    | Support channels
    |--------------------------------------------------------------------------
    |
    | WhatsApp number in international format WITHOUT "+" (e.g. 2499XXXXXXX).
    | Empty = WhatsApp buttons hidden. Shown on /contact, the frozen-account
    | notice and the floating button on every page.
    |
    */
    'whatsapp' => env('SUPPORT_WHATSAPP', ''),
    'support_email' => env('SUPPORT_EMAIL', 'support@example.com'),
];
