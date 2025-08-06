<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RegistroAmbientalResource\Pages;
use App\Filament\Resources\RegistroAmbientalResource\RelationManagers;
use App\Models\RegistroAmbiental;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RegistroAmbientalResource extends Resource
{
    protected static ?string $model = RegistroAmbiental::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('sala_id')
                    ->required()
                    ->numeric(),
                Forms\Components\DateTimePicker::make('fecha_hora'),
                Forms\Components\TextInput::make('temperatura_celsius')
                    ->numeric(),
                Forms\Components\TextInput::make('humedad_relativa')
                    ->numeric(),
                Forms\Components\TextInput::make('co2_ppm')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sala_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_hora')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('temperatura_celsius')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('humedad_relativa')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('co2_ppm')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ListRegistroAmbientals::route('/'),
            'create' => Pages\CreateRegistroAmbiental::route('/create'),
            'edit' => Pages\EditRegistroAmbiental::route('/{record}/edit'),
        ];
    }
}
