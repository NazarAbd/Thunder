<?php

namespace App\Enums;

enum UserRole: string
{
    case User = 'user';
    case Admin = 'admin';

    /**
     * Human-readable label, useful in Blade views or Filament later.
     */
    public function label(): string
    {
        return match ($this) {
            self::User => 'User',
            self::Admin => 'Admin',
        };
    }
}
