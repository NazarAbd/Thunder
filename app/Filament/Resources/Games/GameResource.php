<?php

namespace App\Filament\Resources\Games;

use App\Enums\UserRole;
use App\Filament\Resources\Games\Pages;
use App\Filament\Resources\Games\RelationManagers;
use App\Filament\Resources\Games\Schemas\GameForm;
use App\Filament\Resources\Games\Tables\GamesTable;
use App\Models\Game;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

/**
 * Admin management of games + their services/prices.
 *
 * Position: sidebar, after exchange rates (navigationSort 3).
 * Services are managed inline on the game's Edit page via
 * ServicesRelationManager — no separate sidebar entry.
 *
 * Security: same layered gates as ExchangeRateResource —
 * panel-level admin gate + resource-level role re-checks + form
 * validation. Image uploads are restricted to images (max 5MB) on the
 * `public` disk. No public routes write to these tables.
 */
class GameResource extends Resource
{
    protected static ?string $model = Game::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static ?string $navigationLabel = 'الألعاب والخدمات';

    protected static ?string $modelLabel = 'لعبة';

    protected static ?string $pluralModelLabel = 'الألعاب والخدمات';

    protected static ?int $navigationSort = 3;

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
        return GameForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GamesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ServicesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGames::route('/'),
            'create' => Pages\CreateGame::route('/create'),
            'edit' => Pages\EditGame::route('/{record}/edit'),
        ];
    }
}
