<?php

namespace App\Filament\Resources\LoteInoculoResource\Pages;

use App\Filament\Resources\LoteInoculoResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewLoteInoculo extends ViewRecord
{
    protected static string $resource = LoteInoculoResource::class;

    protected function canView($record): bool
    {
        return auth()->user()?->tienePermiso('lote_inoculo.ver') ?? false;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()->visible(fn() => auth()->user()?->tienePermiso('lote_inoculo.editar') ?? false),
            Actions\DeleteAction::make()->visible(fn() => auth()->user()?->tienePermiso('lote_inoculo.eliminar') ?? false),
        ];
    }
}
