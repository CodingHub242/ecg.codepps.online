<?php

namespace App\Filament\Resources\TheftResource\Pages;

use App\Filament\Resources\TheftResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListThefts extends ListRecords
{
    protected static string $resource = TheftResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
