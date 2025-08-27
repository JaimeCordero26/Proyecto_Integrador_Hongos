<?php

namespace App\Filament\Resources\UnidadProduccionResource\Pages;

use App\Filament\Resources\UnidadProduccionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUnidadProduccion extends CreateRecord
{
    protected static string $resource = UnidadProduccionResource::class;

    protected function canCreate(): bool
    {
        return auth()->user()?->tienePermiso('unidad_produccion.crear') ?? false;
    }
}
