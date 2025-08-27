<?php

namespace App\Filament\Resources\SolicitudProcuraduriaResource\Pages;

use App\Filament\Resources\SolicitudProcuraduriaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSolicitudProcuraduria extends CreateRecord
{
    protected static string $resource = SolicitudProcuraduriaResource::class;

    protected function canCreate(): bool
    {
        return auth()->user()?->tienePermiso('solicitud_procuraduria.crear') ?? false;
    }
}
