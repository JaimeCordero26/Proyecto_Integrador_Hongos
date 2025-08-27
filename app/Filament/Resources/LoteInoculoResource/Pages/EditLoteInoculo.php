<?php

namespace App\Filament\Resources\LoteInoculoResource\Pages;

use App\Filament\Resources\LoteInoculoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLoteInoculo extends EditRecord
{
    protected static string $resource = LoteInoculoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->visible(fn () => auth()->user()?->tienePermiso('lote_inoculo.eliminar') ?? false),
        ];
    }

    protected function canEdit($record): bool
    {
        return auth()->user()?->tienePermiso('lote_inoculo.editar') ?? false;
    }
}
