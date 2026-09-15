<?php

namespace App\Filament\Resources\ExchangeRates;

use App\Enums\UserRole;
use App\Filament\Resources\ExchangeRates\Pages;
use App\Filament\Resources\ExchangeRates\Schemas\ExchangeRateForm;
use App\Filament\Resources\ExchangeRates\Tables\ExchangeRatesTable;
use App\Models\ExchangeRate;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

/**
 * Admin management of the site-wide exchange rate (SDG per 1 USD).
 *
 * Position: sidebar, directly after TopUpRequests
 * (navigationSort 2 vs TopUpRequests' 1).
 *
 * Security layers:
 * 1. Panel level (global): `auth` middleware + User::canAccessPanel()
 *    restricts the whole panel to role=admin users.
 * 2. Resource level (here): canViewAny/canCreate/canEdit/canDelete/
 *    canDeleteAny/canView re-check the admin role, so even if panel
 *    config changes, this resource stays admin-only.
 * 3. Input level: form validation (required, numeric, min 0.01, sane max).
 * 4. Authorship: `created_by` is NOT fillable and is stamped server-side
 *    from auth()->id() in the model — a crafted POST cannot spoof it.
 * 5. No public (non-admin) HTTP routes reference this model.
 */
class ExchangeRateResource extends Resource
{
    protected static ?string $model = ExchangeRate::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationLabel = 'سعر الصرف';

    protected static ?string $modelLabel = 'سعر صرف';

    protected static ?string $pluralModelLabel = 'أسعار الصرف';

    protected static ?int $navigationSort = 2;

    // -----------------------------------------------------------------
    // Authorization (defense in depth on top of panel-level admin gate).
    // -----------------------------------------------------------------

    protected static function isAdmin(): bool
    {
        $user = auth()->user();

        return $user !== null && $user->role === UserRole::Admin;
    }

    public static function canViewAny(): bool
    {
        return static::isAdmin();
    }

    public static function canCreate(): bool
    {
        return static::isAdmin();
    }

    public static function canEdit(Model $record): bool
    {
        return static::isAdmin();
    }

    public static function canDelete(Model $record): bool
    {
        return static::isAdmin();
    }

    public static function canDeleteAny(): bool
    {
        return static::isAdmin();
    }

    public static function canView(Model $record): bool
    {
        return static::isAdmin();
    }

    public static function form(Schema $schema): Schema
    {
        return ExchangeRateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExchangeRatesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExchangeRates::route('/'),
            'create' => Pages\CreateExchangeRate::route('/create'),
            'edit' => Pages\EditExchangeRate::route('/{record}/edit'),
        ];
    }
}
