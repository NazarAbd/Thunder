<?php

namespace App\Filament\Resources\TopUpRequests\Pages;

use App\Filament\Resources\TopUpRequests\TopUpRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTopUpRequest extends CreateRecord
{
    protected static string $resource = TopUpRequestResource::class;
}
