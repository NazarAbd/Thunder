<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Get a setting value, falling back to $default when missing.
     * Used for the site-wide exchange rate so the admin can control
     * it later (Filament UI) without code changes.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $row = static::where('key', $key)->first();

        return $row?->value ?? $default;
    }

    public static function set(string $key, mixed $value): static
    {
        return static::updateOrCreate(['key' => $key], ['value' => (string) $value]);
    }

    /**
     * SDG per 1 USD. Resolution order:
     *  1. Latest active `exchange_rates` row (admin-managed, audited).
     *  2. config/store.php -> env default (only when no row is active).
     *
     * The legacy `settings` key `exchange_rate` is intentionally NOT
     * consulted anymore: it was a stale, UI-less override that could
     * silently reprice the store if the active row was ever removed.
     * The storefront (GameController, GameService) calls this, so an
     * admin rate change takes effect immediately with no deploy.
     */
    public static function exchangeRate(): float
    {
        $current = ExchangeRate::current();

        if ($current !== null && (float) $current->rate > 0) {
            return (float) $current->rate;
        }

        return (float) config('store.exchange_rate', 3000);
    }
}
