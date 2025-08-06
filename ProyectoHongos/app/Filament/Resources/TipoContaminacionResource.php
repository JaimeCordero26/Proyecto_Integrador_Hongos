<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TipoContaminacionResource\Pages;
use App\Filament\Resources\TipoContaminacionResource\RelationManagers;
use App\Models\TipoContaminacion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TipoContaminacionResource extends Resource
{
    protected static ?string $model = TipoContaminacion::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre_comun')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('agente_causal')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre_comun')
                    ->searchable(),
                Tables\Columns\TextColumn::make('agente_causal')
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
            'index' => Pages\ListTipoContaminacions::route('/'),
            'create' => Pages\CreateTipoContaminacion::route('/create'),
            'edit' => Pages\EditTipoContaminacion::route('/{record}/edit'),
        ];
    }
}
