<?php

namespace App\Filament\Resources\CosechaResource\Pages;

use App\Filament\Resources\CosechaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCosecha extends CreateRecord
{
    protected static string $resource = CosechaResource::class;

    protected function canCreate(): bool
    {
        return auth()->user()?->tienePermiso('cosecha.crear') ?? false;
    }
}
