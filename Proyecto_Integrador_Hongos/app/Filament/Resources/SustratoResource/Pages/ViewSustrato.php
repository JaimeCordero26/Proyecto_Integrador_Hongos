<?php

namespace App\Filament\Resources\SustratoResource\Pages;

use App\Filament\Resources\SustratoResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSustrato extends ViewRecord
{
    protected static string $resource = SustratoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
