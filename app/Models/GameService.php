<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A purchasable service of a game (e.g. "600 gems").
 * Price stored in USD; SDG computed on the storefront via exchange rate.
 */
class GameService extends Model
{
    protected $fillable = [
        'game_id',
        'label',
        'price_usd',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price_usd' => 'decimal:2',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * Final SDG price shown to users.
     */
    public function priceSdg(): int
    {
        return (int) round((float) $this->price_usd * Setting::exchangeRate());
    }
}
