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
];
