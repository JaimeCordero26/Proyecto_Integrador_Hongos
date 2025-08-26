<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventarioLaboratorioResource\Pages;
use App\Filament\Resources\InventarioLaboratorioResource\RelationManagers;
use App\Models\InventarioLaboratorio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InventarioLaboratorioResource extends Resource
{
    protected static ?string $model = InventarioLaboratorio::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre_item')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('descripcion')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('cantidad_total')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('cantidad_disponible')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('ubicacion')
                    ->maxLength(255),
                Forms\Components\TextInput::make('estado_item')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre_item')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cantidad_total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cantidad_disponible')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ubicacion')
                    ->searchable(),
                Tables\Columns\TextColumn::make('estado_item')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListInventarioLaboratorios::route('/'),
            'create' => Pages\CreateInventarioLaboratorio::route('/create'),
            'view' => Pages\ViewInventarioLaboratorio::route('/{record}'),
            'edit' => Pages\EditInventarioLaboratorio::route('/{record}/edit'),
        ];
    }
}
