<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SolicitudesProcuraduriaResource\Pages;
use App\Filament\Resources\SolicitudesProcuraduriaResource\RelationManagers;
use App\Models\SolicitudesProcuraduria;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SolicitudesProcuraduriaResource extends Resource
{
    protected static ?string $model = SolicitudesProcuraduria::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSolicitudesProcuradurias::route('/'),
            'create' => Pages\CreateSolicitudesProcuraduria::route('/create'),
            'edit' => Pages\EditSolicitudesProcuraduria::route('/{record}/edit'),
        ];
    }
}
