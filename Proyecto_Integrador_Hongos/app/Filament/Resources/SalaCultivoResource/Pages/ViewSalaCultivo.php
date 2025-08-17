<?php

namespace App\Filament\Resources\SalaCultivoResource\Pages;

use App\Filament\Resources\SalaCultivoResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSalaCultivo extends ViewRecord
{
    protected static string $resource = SalaCultivoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
