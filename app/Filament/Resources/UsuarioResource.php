<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UsuarioResource\Pages;
use App\Models\Usuario;
use App\Models\Rol;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Password as PasswordRule;

class UsuarioResource extends Resource
{
    protected static ?string $model = Usuario::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Usuarios';
    protected static ?string $modelLabel = 'Usuario';
    protected static ?string $pluralModelLabel = 'Usuarios';

    protected static ?string $navigationGroup = 'Seguridad';

    public static function canViewAny(): bool
    {
        return auth()->user()?->tienePermiso('usuarios.ver') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('rol_id')
                ->label('Rol')
                ->options(Rol::query()->pluck('nombre_rol','rol_id'))
                ->searchable()
                ->preload()
                ->required(),
            
            Forms\Components\TextInput::make('nombre_completo')
                ->required()
                ->maxLength(255),
            
            Forms\Components\TextInput::make('email')
                ->email()
                ->required()
                ->unique(ignoreRecord: true, column: 'email'),
            
            Forms\Components\TextInput::make('password')
                ->password()
                ->revealable()
                ->required(function (string $context): bool {
                    return $context === 'create';
                })
                ->dehydrated(fn (?string $state): bool => filled($state))
                ->dehydrateStateUsing(fn (string $state): string => $state)
                ->rules([PasswordRule::min(8)]),
            
            Forms\Components\Toggle::make('activo')
                ->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('rol.nombre_rol')
                ->label('Rol')
                ->badge()
                ->sortable()
                ->toggleable(),
            
            Tables\Columns\TextColumn::make('nombre_completo')
                ->searchable()
                ->sortable(),
            
            Tables\Columns\TextColumn::make('email')
                ->searchable()
                ->sortable(),
            
            Tables\Columns\IconColumn::make('activo')
                ->boolean()
                ->sortable(),
        ])->actions([
            Tables\Actions\ViewAction::make()
                ->visible(fn() => auth()->user()?->tienePermiso('usuarios.ver') ?? false),
            
            Tables\Actions\EditAction::make()
                ->visible(fn() => auth()->user()?->tienePermiso('usuarios.editar') ?? false),
            
            Tables\Actions\DeleteAction::make()
                ->visible(fn() => auth()->user()?->tienePermiso('usuarios.eliminar') ?? false),
        ])->bulkActions([
            Tables\Actions\DeleteBulkAction::make()
                ->visible(fn() => auth()->user()?->tienePermiso('usuarios.eliminar') ?? false),
        ])->defaultSort('usuario_id', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsuarios::route('/'),
            'create' => Pages\CreateUsuario::route('/create'),
            'edit' => Pages\EditUsuario::route('/{record}/edit'),
        ];
    }
}