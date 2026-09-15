<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

/**
 * Admin-managed game.
 *
 * Groups match the homepage sections: direct / account / subscriptions.
 * `created_by` is stamped server-side (not fillable) like ExchangeRate.
 */
class Game extends Model
{
    public const GROUPS = ['direct', 'account', 'subscriptions'];

    protected $fillable = [
        'slug',
        'name',
        'description',
        'image_path',
        'group',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Game $game): void {
            $game->created_by = auth()->id();
        });
    }

    public function services(): HasMany
    {
        return $this->hasMany(GameService::class)->orderBy('sort_order')->orderBy('id');
    }

    public function activeServices(): HasMany
    {
        return $this->services()->where('is_active', true);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Public URL for the game image. Handles storage-disk paths
     * (admin uploads) and legacy public/ relative paths (seeded rows).
     */
    public function getImageUrlAttribute(): string
    {
        $path = (string) $this->image_path;

        if (str_starts_with($path, 'http')) {
            return $path;
        }

        if ($path !== '' && file_exists(public_path($path))) {
            return asset($path);
        }

        return $path !== '' ? Storage::disk('public')->url($path) : '';
    }
}
