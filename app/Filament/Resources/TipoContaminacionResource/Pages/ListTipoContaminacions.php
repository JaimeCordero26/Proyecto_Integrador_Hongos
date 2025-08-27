<?php

namespace App\Filament\Resources\TipoContaminacionResource\Pages;

use App\Filament\Resources\TipoContaminacionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTipoContaminacions extends ListRecords
{
    protected static string $resource = TipoContaminacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->visible(fn () => auth()->user()?->tienePermiso('tipo_contaminacion.crear') ?? false),
        ];
    }
}
