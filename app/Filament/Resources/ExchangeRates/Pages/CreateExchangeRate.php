<?php

namespace App\Filament\Resources\ExchangeRates\Pages;

use App\Filament\Resources\ExchangeRates\ExchangeRateResource;
use Filament\Resources\Pages\CreateRecord;

/**
 * Create page. Author (`created_by`) is stamped server-side by the model,
 * never taken from request input.
 */
class CreateExchangeRate extends CreateRecord
{
    protected static string $resource = ExchangeRateResource::class;
}
