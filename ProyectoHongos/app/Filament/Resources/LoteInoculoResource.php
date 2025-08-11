<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoteInoculoResource\Pages;
use App\Filament\Resources\LoteInoculoResource\RelationManagers;
use App\Models\LoteInoculo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LoteInoculoResource extends Resource
{
    protected static ?string $model = LoteInoculo::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('cepa_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('usuario_creador_id')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('fecha_creacion')
                    ->required(),
                Forms\Components\TextInput::make('sustrato_grano')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('generacion')
                    ->maxLength(255),
                Forms\Components\TextInput::make('proceso_esterilizacion_id')
                    ->numeric(),
                Forms\Components\Textarea::make('notas')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('cepa_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('usuario_creador_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_creacion')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sustrato_grano')
                    ->searchable(),
                Tables\Columns\TextColumn::make('generacion')
                    ->searchable(),
                Tables\Columns\TextColumn::make('proceso_esterilizacion_id')
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
            'index' => Pages\ListLoteInoculos::route('/'),
            'create' => Pages\CreateLoteInoculo::route('/create'),
            'edit' => Pages\EditLoteInoculo::route('/{record}/edit'),
        ];
    }
}
