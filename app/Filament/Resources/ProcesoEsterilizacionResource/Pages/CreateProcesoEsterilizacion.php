<?php

namespace App\Filament\Resources\ProcesoEsterilizacionResource\Pages;

use App\Filament\Resources\ProcesoEsterilizacionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProcesoEsterilizacion extends CreateRecord
{
    protected static string $resource = ProcesoEsterilizacionResource::class;

    protected function canCreate(): bool
    {
        return auth()->user()?->tienePermiso('proceso_esterilizacion.crear') ?? false;
    }
}
