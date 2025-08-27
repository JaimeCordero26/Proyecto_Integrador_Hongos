<?php

namespace App\Filament\Resources\CepaResource\Pages;

use App\Filament\Resources\CepaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCepa extends CreateRecord
{
    protected static string $resource = CepaResource::class;

    protected function canCreate(): bool
    {
        return auth()->user()?->tienePermiso('cepa.crear') ?? false;
    }
}
