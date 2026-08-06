<?php

namespace App\Filament\Resources\TopUpRequests\Pages;

use App\Filament\Resources\TopUpRequests\TopUpRequestResource;
use Filament\Resources\Pages\ListRecords;

class ListTopUpRequests extends ListRecords
{
    protected static string $resource = TopUpRequestResource::class;
}
