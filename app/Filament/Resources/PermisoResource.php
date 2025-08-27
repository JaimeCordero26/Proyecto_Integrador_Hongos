<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Permiso;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Filament\Support\HasCrudPermissions;
use App\Filament\Resources\PermisoResource\Pages;

class PermisoResource extends Resource
{
        use HasCrudPermissions;
    protected static ?string $model = Permiso::class;
    protected static ?string $navigationIcon = 'heroicon-o-key';
    protected static ?string $navigationLabel = 'Permisos';
    protected static ?string $modelLabel = 'Permiso';
    protected static ?string $pluralModelLabel = 'Permisos';
    protected static ?string $recordTitleAttribute = 'nombre_permiso';

    protected static ?string $navigationGroup = 'Seguridad';


    public static function canViewAny(): bool
    {
        return auth()->user()?->tienePermiso('permisos.ver') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nombre_permiso')->required()->maxLength(255)->unique(ignoreRecord: true),
            Forms\Components\Textarea::make('descripcion')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('nombre_permiso')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('descripcion')->limit(80),
        ])->actions([
            Tables\Actions\ViewAction::make()->visible(fn() => auth()->user()?->tienePermiso('permisos.ver') ?? false),
            Tables\Actions\EditAction::make()->visible(fn() => auth()->user()?->tienePermiso('permisos.editar') ?? false),
            Tables\Actions\DeleteAction::make()->visible(fn() => auth()->user()?->tienePermiso('permisos.eliminar') ?? false),
        ])->bulkActions([
            Tables\Actions\DeleteBulkAction::make()->visible(fn() => auth()->user()?->tienePermiso('permisos.eliminar') ?? false),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermisos::route('/'),
            'create' => Pages\CreatePermiso::route('/create'),
            'view' => Pages\ViewPermiso::route('/{record}'),
            'edit' => Pages\EditPermiso::route('/{record}/edit'),
        ];
    }
}
