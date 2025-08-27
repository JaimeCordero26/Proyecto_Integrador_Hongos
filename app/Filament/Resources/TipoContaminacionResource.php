<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\TipoContaminacion;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Support\HasCrudPermissions;


use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TipoContaminacionResource\Pages;
use App\Filament\Resources\TipoContaminacionResource\RelationManagers;

class TipoContaminacionResource extends Resource
{
        use HasCrudPermissions;

        protected static string $permPrefix = 'tipo_contaminacion';

    protected static ?string $model = TipoContaminacion::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';

        protected static ?string $navigationGroup = 'Cultivo';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre_comun')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('agente_causal')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre_comun')
                    ->searchable(),
                Tables\Columns\TextColumn::make('agente_causal')
                    ->searchable(),
            ])
            ->filters([
                //
        ])->actions([
            Tables\Actions\ViewAction::make()->visible(fn() => auth()->user()?->tienePermiso('tipo_contaminacion.ver') ?? false),
            Tables\Actions\EditAction::make()->visible(fn() => auth()->user()?->tienePermiso('tipo_contaminacion.editar') ?? false),
            Tables\Actions\DeleteAction::make()->visible(fn() => auth()->user()?->tienePermiso('tipo_contaminacion.eliminar') ?? false),
        ])->bulkActions([
            Tables\Actions\DeleteBulkAction::make()->visible(fn() => auth()->user()?->tienePermiso('tipo_contaminacion.eliminar') ?? false),
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
            'index' => Pages\ListTipoContaminacions::route('/'),
            'create' => Pages\CreateTipoContaminacion::route('/create'),
            'view' => Pages\ViewTipoContaminacion::route('/{record}'),
            'edit' => Pages\EditTipoContaminacion::route('/{record}/edit'),
        ];
    }
}
