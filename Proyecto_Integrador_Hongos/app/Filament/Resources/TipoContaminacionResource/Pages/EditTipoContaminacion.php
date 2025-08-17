<?php

namespace App\Filament\Resources\TipoContaminacionResource\Pages;

use App\Filament\Resources\TipoContaminacionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTipoContaminacion extends EditRecord
{
    protected static string $resource = TipoContaminacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
