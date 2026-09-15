<?php

namespace App\Filament\Resources\Games\Schemas;

use App\Models\Game;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

/**
 * Game form.
 *
 * - slug: URL key (alpha-dash, unique). Used in /games/{slug}.
 * - group: which homepage section the card is appended to.
 * - image: admin upload (public disk, images only, max 5MB).
 * - sort_order/is_active: ordering + visibility without deleting.
 */
class GameForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('slug')
                    ->label('المعرّف (slug)')
                    ->helperText('يظهر في الرابط: /games/slug — حروف إنجليزية وأرقام وشرطات فقط.')
                    ->required()
                    ->alphaDash()
                    ->maxLength(100)
                    ->unique(ignoreRecord: true),
                TextInput::make('name')
                    ->label('اسم اللعبة')
                    ->required()
                    ->maxLength(255),
                Select::make('group')
                    ->label('القسم في الرئيسية')
                    ->required()
                    ->options([
                        'direct' => '1- الشحن المباشر',
                        'account' => '2- الشحن بالحساب',
                        'subscriptions' => '3- الاشتراكات الرقمية',
                    ])
                    ->default('direct'),
                Textarea::make('description')
                    ->label('الوصف (اختياري)')
                    ->maxLength(1000)
                    ->columnSpanFull(),
                FileUpload::make('image_path')
                    ->label('صورة اللعبة')
                    ->helperText('نفس الصورة تظهر في بطاقة الرئيسية (صغيرة) وصفحة اللعبة (كبيرة).')
                    ->disk('public')
                    ->directory('images/games')
                    ->image()
                    ->maxSize(5120)
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('sort_order')
                    ->label('الترتيب')
                    ->numeric()
                    ->minValue(0)
                    ->default(0),
                Toggle::make('is_active')
                    ->label('ظاهرة في المتجر')
                    ->helperText('إيقافها يخفي البطاقة من الرئيسية ويجعل صفحة اللعبة 404 بدون حذف.')
                    ->default(true),
            ]);
    }

    /**
     * Group labels keyed by Game::GROUPS values, for reuse.
     */
    public static function groupLabels(): array
    {
        return [
            'direct' => '1- الشحن المباشر',
            'account' => '2- الشحن بالحساب',
            'subscriptions' => '3- الاشتراكات الرقمية',
        ];
    }
}
