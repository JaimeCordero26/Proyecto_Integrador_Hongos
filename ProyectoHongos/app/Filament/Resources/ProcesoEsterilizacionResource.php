<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProcesoEsterilizacionResource\Pages;
use App\Filament\Resources\ProcesoEsterilizacionResource\RelationManagers;
use App\Models\ProcesoEsterilizacion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProcesoEsterilizacionResource extends Resource
{
    protected static ?string $model = ProcesoEsterilizacion::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('metodo')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('temperatura_alcanzada_c')
                    ->numeric(),
                Forms\Components\TextInput::make('presion_psi')
                    ->numeric(),
                Forms\Components\TextInput::make('duracion_minutos')
                    ->required()
                    ->numeric(),
                Forms\Components\Textarea::make('descripcion_adicional')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('metodo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('temperatura_alcanzada_c')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('presion_psi')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('duracion_minutos')
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
            'index' => Pages\ListProcesoEsterilizacions::route('/'),
            'create' => Pages\CreateProcesoEsterilizacion::route('/create'),
            'edit' => Pages\EditProcesoEsterilizacion::route('/{record}/edit'),
        ];
    }
}
