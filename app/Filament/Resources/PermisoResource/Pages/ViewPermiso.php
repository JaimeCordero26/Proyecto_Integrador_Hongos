<?php

namespace App\Filament\Resources\PermisoResource\Pages;

use App\Filament\Resources\PermisoResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPermiso extends ViewRecord
{
    protected static string $resource = PermisoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
