<?php

namespace App\Filament\Resources\ExchangeRates\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

/**
 * Exchange-rate form.
 *
 * Validation:
 * - rate: required, numeric, > 0, capped at 1,000,000 to catch typos
 *   (e.g. an extra zero that would 10x every storefront price).
 * - is_active: activating this row auto-deactivates all others
 *   (ExchangeRate::booted saving hook) — single source of truth.
 *
 * `created_by` has no field here on purpose: the model stamps it
 * server-side from the authenticated admin.
 */
class ExchangeRateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('rate')
                    ->label('سعر الصرف (جنيه سوداني لكل 1 دولار)')
                    ->required()
                    ->numeric()
                    ->minValue(0.01)
                    ->maxValue(1000000)
                    ->step(0.01)
                    ->helperText('مثال: 3000 تعني أن عرضاً بسعر 10$ يظهر بـ 30,000 ج.س في صفحة اللعبة.'),
                Toggle::make('is_active')
                    ->label('مفعّل (يُستخدم في المتجر)')
                    ->helperText('تفعيل هذا السعر يعطّل جميع الأسعار الأخرى تلقائياً.')
                    ->default(true),
            ]);
    }
}
