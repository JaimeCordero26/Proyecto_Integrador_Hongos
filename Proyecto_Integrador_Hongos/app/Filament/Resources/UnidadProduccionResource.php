<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UnidadProduccionResource\Pages;
use App\Filament\Resources\UnidadProduccionResource\RelationManagers;
use App\Models\UnidadProduccion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UnidadProduccionResource extends Resource
{
    protected static ?string $model = UnidadProduccion::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('lote_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('codigo_unidad')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('peso_inicial_gramos')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('fecha_inoculacion')
                    ->required(),
                Forms\Components\TextInput::make('estado_unidad')
                    ->maxLength(255),
                Forms\Components\Select::make('tipo_contaminacion_id')
                    ->relationship('tipoContaminacion', 'tipo_contaminacion_id'),
                Forms\Components\Textarea::make('notas_contaminacion')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('lote_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('codigo_unidad')
                    ->searchable(),
                Tables\Columns\TextColumn::make('peso_inicial_gramos')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_inoculacion')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('estado_unidad')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipoContaminacion.tipo_contaminacion_id')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ListUnidadProduccions::route('/'),
            'create' => Pages\CreateUnidadProduccion::route('/create'),
            'view' => Pages\ViewUnidadProduccion::route('/{record}'),
            'edit' => Pages\EditUnidadProduccion::route('/{record}/edit'),
        ];
    }
}
