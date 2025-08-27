<?php

namespace App\Filament\Resources\AccionResource\Pages;

use App\Filament\Resources\AccionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAccions extends ListRecords
{
    protected static string $resource = AccionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->visible(fn () => auth()->user()?->tienePermiso('accion.crear') ?? false),
        ];
    }
}
