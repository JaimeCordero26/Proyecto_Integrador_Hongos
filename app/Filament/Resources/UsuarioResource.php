<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UsuarioResource\Pages;
use App\Models\Usuario;
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

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Rol (usa la relación belongsTo 'rol' del modelo Usuario)
            Forms\Components\Select::make('rol_id')
                ->label('Rol')
                ->relationship('rol', 'nombre_rol') // <- usa rol(): BelongsTo en tu modelo
                ->searchable()
                ->preload()
                ->required(),

            Forms\Components\TextInput::make('nombre_completo')
                ->label('Nombre completo')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('email')

                ->label('Email')
                ->email()
                ->required()
                ->unique(ignoreRecord: true, column: 'email'
                ),

            // IMPORTANTE: atributo 'password' para disparar el mutator -> setPasswordAttribute
            Forms\Components\TextInput::make('password')
                ->label('Contraseña')
                ->password()
                ->revealable()
                ->required(fn (string $operation) => $operation === 'create')
                // Solo se envía al modelo si viene con valor (para no sobreescribir en edición)
                ->dehydrated(fn ($state) => filled($state))
                // Se pasa tal cual; el mutator hará Hash::make y guardará en password_hash
                ->dehydrateStateUsing(fn ($state) => $state)
                ->rule(PasswordRule::min(8)),

            Forms\Components\Toggle::make('activo')
                ->label('Activo')
                ->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Muestra el nombre del rol desde la relación
                Tables\Columns\TextColumn::make('rol.nombre_rol')
                    ->label('Rol')
                    ->badge()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('nombre_completo')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('activo')->label('Activo'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('usuario_id', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers si luego los necesitas
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsuarios::route('/'),
            'create' => Pages\CreateUsuario::route('/create'),
            'edit'   => Pages\EditUsuario::route('/{record}/edit'),
        ];
    }
}
