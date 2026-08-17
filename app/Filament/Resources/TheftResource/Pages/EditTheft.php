<?php

namespace App\Filament\Resources\TheftResource\Pages;

use App\Filament\Resources\TheftResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTheft extends EditRecord
{
    protected static string $resource = TheftResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
