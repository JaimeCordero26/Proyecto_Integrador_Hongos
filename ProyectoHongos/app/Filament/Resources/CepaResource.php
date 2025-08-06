<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CepaResource\Pages;
use App\Filament\Resources\CepaResource\RelationManagers;
use App\Models\Cepa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CepaResource extends Resource
{
    protected static ?string $model = Cepa::class;

    protected static ?string $navigationIcon = 'heroicon-o-ellipsis-horizontal';

    protected static ?string $navigationGroup = 'Cultivo';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre_comun')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nombre_cientifico')
                    ->maxLength(255),
                Forms\Components\TextInput::make('codigo_interno')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre_comun')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nombre_cientifico')
                    ->searchable(),
                Tables\Columns\TextColumn::make('codigo_interno')
                    ->searchable(),
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
            'index' => Pages\ListCepas::route('/'),
            'create' => Pages\CreateCepa::route('/create'),
            'edit' => Pages\EditCepa::route('/{record}/edit'),
        ];
    }
}
