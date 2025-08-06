<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CosechaResource\Pages;
use App\Filament\Resources\CosechaResource\RelationManagers;
use App\Models\Cosecha;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CosechaResource extends Resource
{
    protected static ?string $model = Cosecha::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('unidad_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('numero_cosecha')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('fecha_cosecha')
                    ->required(),
                Forms\Components\TextInput::make('peso_cosecha_gramos')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('eficiencia_biologica_calculada')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('unidad_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('numero_cosecha')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_cosecha')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('peso_cosecha_gramos')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('eficiencia_biologica_calculada')
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
            'index' => Pages\ListCosechas::route('/'),
            'create' => Pages\CreateCosecha::route('/create'),
            'edit' => Pages\EditCosecha::route('/{record}/edit'),
        ];
    }
}
