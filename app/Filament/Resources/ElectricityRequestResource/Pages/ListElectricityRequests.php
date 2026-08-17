<?php

namespace App\Filament\Resources\ElectricityRequestResource\Pages;

use App\Filament\Resources\ElectricityRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListElectricityRequests extends ListRecords
{
    protected static string $resource = ElectricityRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
