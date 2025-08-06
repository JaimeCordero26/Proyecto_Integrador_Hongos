<?php

namespace App\Filament\Resources;

use App\Models\InventarioLaboratorio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\InventarioLaboratorioResource\Pages;

class InventarioLaboratorioResource extends Resource
{
    protected static ?string $model = InventarioLaboratorio::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationGroup = 'Administración';
    protected static ?string $navigationLabel = 'Inventario Laboratorios';
    protected static ?string $pluralModelLabel = 'Inventario Laboratorios';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre_item')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('descripcion')
                    ->rows(3)
                    ->maxLength(500),

                Forms\Components\TextInput::make('cantidad_total')
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('cantidad_disponible')
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('ubicacion')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('estado_item')
                    ->required()
                    ->maxLength(100),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre_item')->numeric()->searchable()->sortable(),
                Tables\Columns\TextColumn::make('descripcion')->limit(30)->numeric()->searchable()->sortable(),
                Tables\Columns\TextColumn::make('cantidad_total')->numeric()->searchable()->sortable(),
                Tables\Columns\TextColumn::make('cantidad_disponible')->numeric()->searchable()->sortable(),
                Tables\Columns\TextColumn::make('ubicacion')->numeric()->searchable()->sortable(),
                Tables\Columns\TextColumn::make('estado_item')->numeric()->searchable()->sortable(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventarioLaboratorios::route('/'),
            'create' => Pages\CreateInventarioLaboratorio::route('/create'),
            'edit' => Pages\EditInventarioLaboratorio::route('/{record}/edit'),
        ];
    }
}
