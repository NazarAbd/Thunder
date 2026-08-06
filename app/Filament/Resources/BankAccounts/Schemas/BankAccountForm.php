<?php

namespace App\Filament\Resources\BankAccounts\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BankAccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
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
}
