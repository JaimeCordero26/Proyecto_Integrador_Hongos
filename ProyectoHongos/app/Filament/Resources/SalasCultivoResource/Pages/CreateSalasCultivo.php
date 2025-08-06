<?php

namespace App\Filament\Resources\SalasCultivoResource\Pages;

use App\Filament\Resources\SalasCultivoResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSalasCultivo extends CreateRecord
{
    protected static string $resource = SalasCultivoResource::class;
    
    protected function getRedirectUrl(): string
    {
        
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Elemento agregado correctamente';
    }
}