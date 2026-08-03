<?php

namespace App\Filament\Resources\TopUpRequests\Pages;

use App\Filament\Resources\TopUpRequests\TopUpRequestResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTopUpRequest extends EditRecord
{
    protected static string $resource = TopUpRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
