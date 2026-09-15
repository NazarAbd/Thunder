<?php

namespace App\Filament\Resources\Games\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Games list. Shows service counts so the admin sees pricing coverage
 * at a glance. Deleting a game cascades to its services (FK).
 */
class GamesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_path')
                    ->label('الصورة')
                    ->disk('public')
                    ->height(50)
                    ->width(50),
                TextColumn::make('name')->label('اللعبة')->searchable(),
                TextColumn::make('slug')->label('المعرّف')->searchable()->toggleable(),
                TextColumn::make('group')->label('القسم')->badge()->formatStateUsing(
                    fn (string $state): string => \App\Filament\Resources\Games\Schemas\GameForm::groupLabels()[$state] ?? $state,
                ),
                TextColumn::make('services_count')
                    ->label('الخدمات')
                    ->counts('services')
                    ->sortable(),
                IconColumn::make('is_active')->label('ظاهرة')->boolean(),
                TextColumn::make('creator.name')->label('بواسطة')->placeholder('—')->toggleable(),
                TextColumn::make('created_at')->label('أُنشئت في')->dateTime('Y-m-d H:i')->sortable()->toggleable(),
            ])
            ->defaultSort('sort_order')
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
