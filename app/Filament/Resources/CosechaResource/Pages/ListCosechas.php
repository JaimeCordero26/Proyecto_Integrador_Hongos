<?php

namespace App\Filament\Resources\CosechaResource\Pages;

use App\Filament\Resources\CosechaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCosechas extends ListRecords
{
    protected static string $resource = CosechaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->visible(fn () => auth()->user()?->tienePermiso('cosecha.crear') ?? false),
        ];
    }
}
