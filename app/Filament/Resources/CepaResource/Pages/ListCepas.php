<?php

namespace App\Filament\Resources\CepaResource\Pages;

use App\Filament\Resources\CepaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCepas extends ListRecords
{
    protected static string $resource = CepaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->visible(fn () => auth()->user()?->tienePermiso('cepa.crear') ?? false),
        ];
    }
}
