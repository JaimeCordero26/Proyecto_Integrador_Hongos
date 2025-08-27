<?php

namespace App\Filament\Resources\BitacoraActividadResource\Pages;

use App\Filament\Resources\BitacoraActividadResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBitacoraActividad extends CreateRecord
{
    protected static string $resource = BitacoraActividadResource::class;

    protected function canCreate(): bool
    {
        return auth()->user()?->tienePermiso('bitacora_actividad.crear') ?? false;
    }
}
