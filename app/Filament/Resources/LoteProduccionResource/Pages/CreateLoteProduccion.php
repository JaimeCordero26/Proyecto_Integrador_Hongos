<?php

namespace App\Filament\Resources\LoteProduccionResource\Pages;

use App\Filament\Resources\LoteProduccionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLoteProduccion extends CreateRecord
{
    protected static string $resource = LoteProduccionResource::class;

    protected function canCreate(): bool
    {
        return auth()->user()?->tienePermiso('lote_produccion.crear') ?? false;
    }
}
