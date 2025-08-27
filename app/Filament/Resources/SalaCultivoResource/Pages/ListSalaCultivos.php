<?php

namespace App\Filament\Resources\SalaCultivoResource\Pages;

use App\Filament\Resources\SalaCultivoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSalaCultivos extends ListRecords
{
    protected static string $resource = SalaCultivoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->visible(fn () => auth()->user()?->tienePermiso('sala_cultivo.crear') ?? false),
        ];
    }
}
