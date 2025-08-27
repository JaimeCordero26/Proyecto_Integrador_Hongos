<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Cepa;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Support\HasCrudPermissions;
use App\Filament\Resources\CepaResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CepaResource\RelationManagers;

class CepaResource extends Resource
{
        use HasCrudPermissions;
    protected static ?string $model = Cepa::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Cultivo';

    protected static string $permPrefix = 'cepa';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre_comun')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nombre_cientifico')
                    ->maxLength(255),
                Forms\Components\TextInput::make('codigo_interno')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre_comun')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nombre_cientifico')
                    ->searchable(),
                Tables\Columns\TextColumn::make('codigo_interno')
                    ->searchable(),
            ])
            ->filters([
                //
            ])->actions([
                Tables\Actions\ViewAction::make()->visible(fn () => auth()->user()?->tienePermiso('cepa.ver') ?? false),
                Tables\Actions\EditAction::make()->visible(fn () => auth()->user()?->tienePermiso('cepa.editar') ?? false),
                Tables\Actions\DeleteAction::make()->visible(fn () => auth()->user()?->tienePermiso('cepa.eliminar') ?? false),
            ])->bulkActions([
                Tables\Actions\DeleteBulkAction::make()->visible(fn() => auth()->user()?->tienePermiso('cepa.eliminar') ?? false),
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
            'index' => Pages\ListCepas::route('/'),
            'create' => Pages\CreateCepa::route('/create'),
            'view' => Pages\ViewCepa::route('/{record}'),
            'edit' => Pages\EditCepa::route('/{record}/edit'),
        ];
    }
}
