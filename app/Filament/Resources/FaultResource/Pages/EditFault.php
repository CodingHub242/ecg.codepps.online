<?php

namespace App\Filament\Resources\FaultResource\Pages;

use App\Filament\Resources\FaultResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFault extends EditRecord
{
    protected static string $resource = FaultResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
