<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalasCultivoResource\Pages;
use App\Filament\Resources\SalasCultivoResource\RelationManagers;
use App\Models\SalasCultivo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SalasCultivoResource extends Resource
{
    protected static ?string $model = SalasCultivo::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox';
    protected static ?string $navigationGroup = 'Cultivo';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre_sala')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('descripcion')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('proposito')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre_sala')
                    ->searchable(),
                Tables\Columns\TextColumn::make('proposito')
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
            'index' => Pages\ListSalasCultivos::route('/'),
            'create' => Pages\CreateSalasCultivo::route('/create'),
            'edit' => Pages\EditSalasCultivo::route('/{record}/edit'),
        ];
    }
}
