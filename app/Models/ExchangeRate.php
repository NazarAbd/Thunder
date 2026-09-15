<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Site-wide exchange rate (SDG per 1 USD) history row.
 *
 * Invariants (enforced in booted()):
 * - Rate must be > 0 (also validated in the Filament form).
 * - At most one row is active at a time: activating a row deactivates
 *   all others, so the storefront always has a single source of truth.
 * - `created_by` is stamped from the authenticated admin on create and
 *   is never mass-assignable from request data (see $fillable + resource
 *   mutateFormDataUsing / booted creating hook).
 */
class ExchangeRate extends Model
{
    /**
     * NOTE: `created_by` is intentionally NOT fillable. It is stamped
     * server-side from auth()->id() in booted(), so a crafted POST can
     * never impersonate another admin as the author.
     */
    protected $fillable = [
        'rate',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'rate' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        // Stamp the author server-side; ignore any client-supplied value.
        static::creating(function (ExchangeRate $row): void {
            $row->created_by = auth()->id();
        });

        // Single-active invariant: activating one row deactivates the rest.
        static::saving(function (ExchangeRate $row): void {
            if ($row->is_active) {
                static::where('id', '!=', $row->id ?? 0)
                    ->where('is_active', true)
                    ->update(['is_active' => false]);
            }
        });
    }

    /**
     * The currently effective rate (latest active row), if any.
     */
    public static function current(): ?static
    {
        return static::where('is_active', true)->latest('id')->first();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Admin who set this rate. Null when that user was deleted
     * (nullOnDelete) — history row itself is kept.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
