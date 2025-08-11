<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BitacoraActividadesResource\Pages;
use App\Filament\Resources\BitacoraActividadesResource\RelationManagers;
use App\Models\BitacoraActividades;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BitacoraActividadesResource extends Resource
{
    protected static ?string $model = BitacoraActividades::class;

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
            'index' => Pages\ListBitacoraActividades::route('/'),
            'create' => Pages\CreateBitacoraActividades::route('/create'),
            'edit' => Pages\EditBitacoraActividades::route('/{record}/edit'),
        ];
    }
}
