<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

/**
 * Site-wide exchange rate (SDG per 1 USD) history row.
 *
 * Invariants (enforced in booted()):
 * - Rate must be > 0 (also validated in the Filament form).
 * - At most one row is active at a time: activating a row deactivates
 *   all others, so the storefront always has a single source of truth.
 * - The sole active row can neither be deactivated nor deleted, so the
 *   storefront never silently falls back to the config default.
 *   Activate a replacement first.
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

        // Never leave the store without a live rate: the sole active row
        // cannot be deactivated. Activate a replacement first.
        static::saving(function (ExchangeRate $row): void {
            if (
                $row->exists
                && $row->isDirty('is_active')
                && ! $row->is_active
                && ! static::where('is_active', true)->where('id', '!=', $row->id)->exists()
            ) {
                throw ValidationException::withMessages([
                    'is_active' => 'لا يمكن إلغاء تفعيل آخر سعر صرف مفعّل. فعّل سعراً آخر أولاً.',
                ]);
            }
        });

        // Same protection for deletes (single + bulk): the sole active
        // row cannot be removed while it is the store's live rate.
        static::deleting(function (ExchangeRate $row): void {
            if (
                $row->is_active
                && ! static::where('is_active', true)->where('id', '!=', $row->id)->exists()
            ) {
                throw ValidationException::withMessages([
                    'is_active' => 'لا يمكن حذف آخر سعر صرف مفعّل. أضف سعراً بديلاً وفعّله أولاً.',
                ]);
            }
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
