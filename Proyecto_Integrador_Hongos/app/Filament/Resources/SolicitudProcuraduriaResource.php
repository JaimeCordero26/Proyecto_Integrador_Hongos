<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SolicitudProcuraduriaResource\Pages;
use App\Filament\Resources\SolicitudProcuraduriaResource\RelationManagers;
use App\Models\SolicitudProcuraduria;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SolicitudProcuraduriaResource extends Resource
{
    protected static ?string $model = SolicitudProcuraduria::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('item_id')
                    ->numeric(),
                Forms\Components\TextInput::make('descripcion_item_nuevo')
                    ->maxLength(255),
                Forms\Components\Select::make('usuario_solicitante_id')
                    ->relationship('usuarioSolicitante', 'usuario_id')
                    ->required(),
                Forms\Components\DateTimePicker::make('fecha_solicitud'),
                Forms\Components\TextInput::make('cantidad_solicitada')
                    ->required()
                    ->numeric(),
                Forms\Components\Textarea::make('justificacion')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('estado_solicitud')
                    ->maxLength(255)
                    ->default('Pendiente'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('item_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('descripcion_item_nuevo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('usuarioSolicitante.usuario_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_solicitud')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cantidad_solicitada')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('estado_solicitud')
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
            'index' => Pages\ListSolicitudProcuradurias::route('/'),
            'create' => Pages\CreateSolicitudProcuraduria::route('/create'),
            'view' => Pages\ViewSolicitudProcuraduria::route('/{record}'),
            'edit' => Pages\EditSolicitudProcuraduria::route('/{record}/edit'),
        ];
    }
}
