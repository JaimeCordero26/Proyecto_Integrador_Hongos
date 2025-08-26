<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AccionResource\Pages;
use App\Filament\Resources\AccionResource\RelationManagers;
use App\Models\Accion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AccionResource extends Resource
{
    protected static ?string $model = Accion::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('tabla_afectada')
                    ->maxLength(100),
                Forms\Components\TextInput::make('id_registro')
                    ->numeric(),
                Forms\Components\TextInput::make('tipo_accion')
                    ->maxLength(10),
                Forms\Components\TextInput::make('usuario_id')
                    ->numeric(),
                Forms\Components\DateTimePicker::make('fecha_hora'),
                Forms\Components\Textarea::make('descripcion')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tabla_afectada')
                    ->searchable(),
                Tables\Columns\TextColumn::make('id_registro')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tipo_accion')
                    ->searchable(),
                Tables\Columns\TextColumn::make('usuario_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_hora')
                    ->dateTime()
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
            'index' => Pages\ListAccions::route('/'),
            'create' => Pages\CreateAccion::route('/create'),
            'view' => Pages\ViewAccion::route('/{record}'),
            'edit' => Pages\EditAccion::route('/{record}/edit'),
        ];
    }
}
