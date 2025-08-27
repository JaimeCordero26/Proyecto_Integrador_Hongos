<?php

namespace App\Filament\Resources\RegistroAmbientalResource\Pages;

use App\Filament\Resources\RegistroAmbientalResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRegistroAmbiental extends CreateRecord
{
    protected static string $resource = RegistroAmbientalResource::class;

    protected function canCreate(): bool
    {
        return auth()->user()?->tienePermiso('registro_ambiental.crear') ?? false;
    }
}
