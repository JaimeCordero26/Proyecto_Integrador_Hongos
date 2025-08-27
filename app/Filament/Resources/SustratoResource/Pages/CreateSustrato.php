<?php

namespace App\Filament\Resources\SustratoResource\Pages;

use App\Filament\Resources\SustratoResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSustrato extends CreateRecord
{
    protected static string $resource = SustratoResource::class;

    protected function canCreate(): bool
    {
        return auth()->user()?->tienePermiso('sustrato.crear') ?? false;
    }
}
