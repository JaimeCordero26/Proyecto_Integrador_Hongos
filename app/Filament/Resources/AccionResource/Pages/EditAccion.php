<?php

namespace App\Filament\Resources\AccionResource\Pages;

use App\Filament\Resources\AccionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAccion extends EditRecord
{
    protected static string $resource = AccionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
