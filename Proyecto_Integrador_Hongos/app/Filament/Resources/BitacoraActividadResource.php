<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BitacoraActividadResource\Pages;
use App\Filament\Resources\BitacoraActividadResource\RelationManagers;
use App\Models\BitacoraActividad;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BitacoraActividadResource extends Resource
{
    protected static ?string $model = BitacoraActividad::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('usuario_id')
                    ->relationship('usuario', 'usuario_id')
                    ->required(),
                Forms\Components\DateTimePicker::make('fecha_hora'),
                Forms\Components\TextInput::make('tipo_actividad')
                    ->maxLength(255),
                Forms\Components\Textarea::make('descripcion_detallada')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('usuario.usuario_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_hora')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tipo_actividad')
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
            'index' => Pages\ListBitacoraActividads::route('/'),
            'create' => Pages\CreateBitacoraActividad::route('/create'),
            'view' => Pages\ViewBitacoraActividad::route('/{record}'),
            'edit' => Pages\EditBitacoraActividad::route('/{record}/edit'),
        ];
    }
}
