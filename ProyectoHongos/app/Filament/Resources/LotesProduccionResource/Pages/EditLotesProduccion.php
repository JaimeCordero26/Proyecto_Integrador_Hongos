<?php

namespace App\Filament\Resources\LotesProduccionResource\Pages;

use App\Filament\Resources\LotesProduccionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLotesProduccion extends EditRecord
{
    protected static string $resource = LotesProduccionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
