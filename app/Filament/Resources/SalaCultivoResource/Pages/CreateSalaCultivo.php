<?php

namespace App\Filament\Resources\SalaCultivoResource\Pages;

use App\Filament\Resources\SalaCultivoResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSalaCultivo extends CreateRecord
{
    protected static string $resource = SalaCultivoResource::class;

    protected function canCreate(): bool
    {
        return auth()->user()?->tienePermiso('sala_cultivo.crear') ?? false;
    }
}
