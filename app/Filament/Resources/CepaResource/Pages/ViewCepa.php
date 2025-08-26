<?php

namespace App\Filament\Resources\CepaResource\Pages;

use App\Filament\Resources\CepaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCepa extends ViewRecord
{
    protected static string $resource = CepaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
