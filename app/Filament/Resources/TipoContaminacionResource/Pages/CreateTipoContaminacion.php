<?php

namespace App\Filament\Resources\TipoContaminacionResource\Pages;

use App\Filament\Resources\TipoContaminacionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTipoContaminacion extends CreateRecord
{
    protected static string $resource = TipoContaminacionResource::class;

    protected function canCreate(): bool
    {
        return auth()->user()?->tienePermiso('tipo_contaminacion.crear') ?? false;
    }
}
