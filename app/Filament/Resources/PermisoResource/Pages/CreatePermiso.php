<?php

namespace App\Filament\Resources\PermisoResource\Pages;

use App\Filament\Resources\PermisoResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePermiso extends CreateRecord
{
    protected static string $resource = PermisoResource::class;

    protected function canCreate(): bool
    {
        return auth()->user()?->tienePermiso('permisos.crear') ?? false;
    }
}
