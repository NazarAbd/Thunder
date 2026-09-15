<?php

namespace App\Filament\Resources\Games\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Inline services/prices manager on the game's Edit page.
 *
 * Prices are entered in USD; the storefront converts to SDG with the
 * active exchange rate. Validation mirrors the exchange-rate form:
 * required, numeric, > 0, capped to catch typos.
 */
class ServicesRelationManager extends RelationManager
{
    protected static string $relationship = 'services';

    protected static ?string $title = 'الخدمات والأسعار';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('label')
                ->label('اسم الخدمة')
                ->helperText('مثال: 600 gems')
                ->required()
                ->maxLength(255),
            TextInput::make('price_usd')
                ->label('السعر بالدولار (USD)')
                ->helperText('يُحوَّل تلقائياً للجنيه حسب سعر الصرف المفعّل.')
                ->required()
                ->numeric()
                ->minValue(0.01)
                ->maxValue(1000000)
                ->step(0.01),
            TextInput::make('sort_order')
                ->label('الترتيب')
                ->numeric()
                ->minValue(0)
                ->default(0),
            Toggle::make('is_active')
                ->label('ظاهرة في المتجر')
                ->default(true),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')->label('الخدمة')->searchable(),
                TextColumn::make('price_usd')->label('السعر ($)')->numeric(2)->sortable(),
                IconColumn::make('is_active')->label('ظاهرة')->boolean(),
                TextColumn::make('sort_order')->label('الترتيب')->sortable()->toggleable(),
            ])
            ->defaultSort('sort_order')
            ->headerActions([
                CreateAction::make()->label('خدمة جديدة'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
