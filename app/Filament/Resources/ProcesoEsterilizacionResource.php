<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\ProcesoEsterilizacion;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Support\HasCrudPermissions;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProcesoEsterilizacionResource\Pages;
use App\Filament\Resources\ProcesoEsterilizacionResource\RelationManagers;

class ProcesoEsterilizacionResource extends Resource
{
        use HasCrudPermissions;

        protected static string $permPrefix = 'proceso_esterilizacion';

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
            'index' => Pages\ListProcesoEsterilizacions::route('/'),
            'create' => Pages\CreateProcesoEsterilizacion::route('/create'),
            'view' => Pages\ViewProcesoEsterilizacion::route('/{record}'),
            'edit' => Pages\EditProcesoEsterilizacion::route('/{record}/edit'),
        ];
    }
}
