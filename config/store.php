<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Site-wide exchange rate (SDG per 1 USD)
    |--------------------------------------------------------------------------
    |
    | All game offer prices are stored in USD and converted to Sudanese
    | pounds with this rate. The `settings` table value (key
    | `exchange_rate`) wins when present so the admin can control it
    | later from the dashboard without touching code.
    |
    */
    'exchange_rate' => (float) env('EXCHANGE_RATE_SDG_PER_USD', 3000),
];
