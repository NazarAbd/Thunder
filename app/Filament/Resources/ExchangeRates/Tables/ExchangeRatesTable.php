<?php

namespace App\Filament\Resources\ExchangeRates\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Exchange-rate history table.
 *
 * Shows the full audit trail (who set what, when). The active row is the
 * one the storefront prices from. The sole active row is protected by
 * the model: it can neither be deactivated nor deleted until a
 * replacement is activated — otherwise the store would silently fall
 * back to the config default (see Setting::exchangeRate()).
 */
class ExchangeRatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('rate')
                    ->label('السعر (ج.س / $)')
                    ->numeric(2)
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('مفعّل')
                    ->boolean(),
                TextColumn::make('creator.name')
                    ->label('بواسطة')
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('أُنشئ في')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
