<?php

namespace App\Filament\Resources\SustratoResource\Pages;

use App\Filament\Resources\SustratoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSustrato extends EditRecord
{
    protected static string $resource = SustratoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
