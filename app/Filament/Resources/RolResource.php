<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RolResource\Pages;
use App\Filament\Resources\RolResource\RelationManagers\PermisosRelationManager;
use App\Models\Rol;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RolResource extends Resource
{
    protected static ?string $model = Rol::class;
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Roles';
    protected static ?string $modelLabel = 'Rol';
    protected static ?string $pluralModelLabel = 'Roles';

    protected static ?string $navigationGroup = 'Seguridad';


    public static function canViewAny(): bool
    {
        return auth()->user()?->tienePermiso('roles.ver') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nombre_rol')->required()->maxLength(255)->unique(ignoreRecord: true),
            Forms\Components\Textarea::make('descripcion_rol'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('nombre_rol')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('descripcion_rol')->limit(80),
            Tables\Columns\TextColumn::make('permisos_count')->counts('permisos')->label('Permisos'),
        ])->actions([
            Tables\Actions\ViewAction::make()->visible(fn() => auth()->user()?->tienePermiso('roles.ver') ?? false),
            Tables\Actions\EditAction::make()->visible(fn() => auth()->user()?->tienePermiso('roles.editar') ?? false),
            Tables\Actions\DeleteAction::make()->visible(fn() => auth()->user()?->tienePermiso('roles.eliminar') ?? false),
        ])->bulkActions([
            Tables\Actions\DeleteBulkAction::make()->visible(fn() => auth()->user()?->tienePermiso('roles.eliminar') ?? false),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            PermisosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRols::route('/'),
            'create' => Pages\CreateRol::route('/create'),
            'view' => Pages\ViewRol::route('/{record}'),
            'edit' => Pages\EditRol::route('/{record}/edit'),
        ];
    }
}
