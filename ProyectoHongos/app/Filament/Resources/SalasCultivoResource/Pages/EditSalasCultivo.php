<?php

namespace App\Filament\Resources\SalasCultivoResource\Pages;

use App\Filament\Resources\SalasCultivoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSalasCultivo extends EditRecord
{
    protected static string $resource = SalasCultivoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
