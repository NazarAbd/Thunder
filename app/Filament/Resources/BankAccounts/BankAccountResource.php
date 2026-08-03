<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BankAccountResource\Pages;
use App\Models\BankAccount;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use BackedEnum;

class BankAccountResource extends Resource
{
    protected static ?string $model = BankAccount::class;

    // protected static ?string $navigationIcon = 'heroicon-o-building-library';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-library';

    protected static ?string $navigationLabel = 'الحسابات البنكية';

    protected static ?string $modelLabel = 'حساب بنكي';

    protected static ?string $pluralModelLabel = 'الحسابات البنكية';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('bank_name')
                ->label('اسم البنك')
                ->required()
                ->maxLength(255),
            TextInput::make('account_holder_name')
                ->label('اسم صاحب الحساب')
                ->required()
                ->maxLength(255),
            TextInput::make('account_number')
                ->label('رقم الحساب')
                ->required()
                ->maxLength(255),
            TextInput::make('iban')
                ->label('IBAN (اختياري)')
                ->maxLength(255),
            Textarea::make('notes')
                ->label('ملاحظات تظهر للمستخدم (اختياري)')
                ->maxLength(500)
                ->columnSpanFull(),
            Toggle::make('is_active')
                ->label('مفعّل ويظهر للمستخدمين')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('bank_name')->label('اسم البنك')->searchable(),
                TextColumn::make('account_holder_name')->label('صاحب الحساب')->searchable(),
                TextColumn::make('account_number')->label('رقم الحساب'),
                IconColumn::make('is_active')->label('مفعّل')->boolean(),
                TextColumn::make('created_at')->label('أُنشئ في')->dateTime('Y-m-d H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBankAccounts::route('/'),
            'create' => Pages\CreateBankAccount::route('/create'),
            'edit' => Pages\EditBankAccount::route('/{record}/edit'),
        ];
    }
}
