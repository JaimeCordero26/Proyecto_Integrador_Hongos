<?php

namespace App\Filament\Resources\SustratoResource\Pages;

use App\Filament\Resources\SustratoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSustratos extends ListRecords
{
    protected static string $resource = SustratoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->visible(fn () => auth()->user()?->tienePermiso('sustrato.crear') ?? false),
        ];
    }
}
