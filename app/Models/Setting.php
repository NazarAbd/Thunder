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
     *  2. Legacy `settings` key `exchange_rate` (kept as fallback).
     *  3. config/store.php -> env default.
     *
     * The storefront (GameController) calls this, so an admin rate change
     * takes effect immediately with no deploy and no public exposure of
     * the rate itself.
     */
    public static function exchangeRate(): float
    {
        $current = ExchangeRate::current();

        if ($current !== null && (float) $current->rate > 0) {
            return (float) $current->rate;
        }

        return (float) static::get('exchange_rate', config('store.exchange_rate', 3000));
    }
}
