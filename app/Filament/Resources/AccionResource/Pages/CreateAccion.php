<?php

namespace App\Filament\Resources\AccionResource\Pages;

use App\Filament\Resources\AccionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAccion extends CreateRecord
{
    protected static string $resource = AccionResource::class;

    protected function canCreate(): bool
    {
        return auth()->user()?->tienePermiso('accion.crear') ?? false;
    }
}
