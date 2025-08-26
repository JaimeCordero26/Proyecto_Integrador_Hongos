<?php

namespace App\Filament\Resources\LoteInoculoResource\Pages;

use App\Filament\Resources\LoteInoculoResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewLoteInoculo extends ViewRecord
{
    protected static string $resource = LoteInoculoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
