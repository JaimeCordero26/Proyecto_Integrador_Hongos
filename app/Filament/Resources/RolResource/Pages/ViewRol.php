<?php

namespace App\Filament\Resources\RolResource\Pages;

use App\Filament\Resources\RolResource;
use Filament\Resources\Pages\ViewRecord;

class ViewRol extends ViewRecord
{
    protected static string $resource = RolResource::class;

    protected function canView(): bool
    {
        return auth()->user()?->tienePermiso('roles.ver') ?? false;
    }
}
