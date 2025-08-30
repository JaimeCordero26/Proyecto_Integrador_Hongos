<?php

namespace App\Filament\Resources\RolResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;

class PermisosRelationManager extends RelationManager
{
    protected static string $relationship = 'permisos';
    protected static ?string $inverseRelationship = 'roles';
    protected static ?string $title = 'Permisos';
    protected static ?string $recordTitleAttribute = 'nombre_permiso';

    public static function canViewForRecord($ownerRecord, $page): bool
    {
        return (auth()->user()?->tienePermiso('roles.editar') ?? false) || (auth()->user()?->tienePermiso('permisos.ver') ?? false);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('permiso_id')->relationship('permisos', 'nombre_permiso')->searchable()->preload()->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('nombre_permiso')->label('Permiso')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('descripcion')->limit(80),
        ])->headerActions([
            Tables\Actions\AttachAction::make()->visible(fn() => auth()->user()?->tienePermiso('roles.editar') ?? false),
        ])->actions([
            Tables\Actions\DetachAction::make()->visible(fn() => auth()->user()?->tienePermiso('roles.editar') ?? false),
        ]);
    }
}
