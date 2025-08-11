<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LotesProduccionResource\Pages;
use App\Filament\Resources\LotesProduccionResource\RelationManagers;
use App\Models\LotesProduccion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LotesProduccionResource extends Resource
{
    protected static ?string $model = LotesProduccion::class;

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
            'index' => Pages\ListLotesProduccions::route('/'),
            'create' => Pages\CreateLotesProduccion::route('/create'),
            'edit' => Pages\EditLotesProduccion::route('/{record}/edit'),
        ];
    }
}
