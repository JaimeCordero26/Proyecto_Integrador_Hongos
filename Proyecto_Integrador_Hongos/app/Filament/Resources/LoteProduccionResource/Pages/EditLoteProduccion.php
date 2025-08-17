<?php

namespace App\Filament\Resources\LoteProduccionResource\Pages;

use App\Filament\Resources\LoteProduccionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLoteProduccion extends EditRecord
{
    protected static string $resource = LoteProduccionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
