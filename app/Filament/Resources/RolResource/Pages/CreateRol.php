<?php

namespace App\Filament\Resources\RolResource\Pages;

use App\Filament\Resources\RolResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRol extends CreateRecord
{
    protected static string $resource = RolResource::class;

    protected function canCreate(): bool
    {
        return auth()->user()?->tienePermiso('roles.crear') ?? false;
    }
}
