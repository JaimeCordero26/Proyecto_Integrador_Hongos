<?php

namespace App\Filament\Resources\CepaResource\Pages;

use App\Filament\Resources\CepaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCepa extends EditRecord
{
    protected static string $resource = CepaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
