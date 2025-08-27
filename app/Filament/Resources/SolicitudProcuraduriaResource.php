<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\SolicitudProcuraduria;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Support\HasCrudPermissions;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SolicitudProcuraduriaResource\Pages;
use App\Filament\Resources\SolicitudProcuraduriaResource\RelationManagers;

class SolicitudProcuraduriaResource extends Resource
{
        use HasCrudPermissions;

        protected static string $permPrefix = 'solicitud_procuraduria';

    protected static ?string $model = SolicitudProcuraduria::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Administración';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('item_id') // <-- Cambiado a Select para un combobox
                    ->label('Item de Inventario')
                    ->relationship('item', 'nombre_item')
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('descripcion_item_nuevo')
                    ->maxLength(255),
                Forms\Components\Select::make('usuario_solicitante_id')
                    ->relationship('usuarioSolicitante', 'usuario_id')
                    ->required(),
                Forms\Components\DateTimePicker::make('fecha_solicitud'),
                Forms\Components\TextInput::make('cantidad_solicitada')
                    ->required()
                    ->numeric(),
                Forms\Components\Textarea::make('justificacion')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('estado_solicitud')
                    ->maxLength(255)
                    ->default('Pendiente'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('item_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('descripcion_item_nuevo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('usuarioSolicitante.usuario_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_solicitud')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cantidad_solicitada')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('estado_solicitud')
                    ->searchable(),
            ])
            ->filters([
                //
        ])->actions([
            Tables\Actions\ViewAction::make()->visible(fn() => auth()->user()?->tienePermiso('solicitud_procuraduria.ver') ?? false),
            Tables\Actions\EditAction::make()->visible(fn() => auth()->user()?->tienePermiso('solicitud_procuraduria.editar') ?? false),
            Tables\Actions\DeleteAction::make()->visible(fn() => auth()->user()?->tienePermiso('solicitud_procuraduria.eliminar') ?? false),
        ])->bulkActions([
            Tables\Actions\DeleteBulkAction::make()->visible(fn() => auth()->user()?->tienePermiso('solicitud_procuraduria.eliminar') ?? false),
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
            'index' => Pages\ListSolicitudProcuradurias::route('/'),
            'create' => Pages\CreateSolicitudProcuraduria::route('/create'),
            'view' => Pages\ViewSolicitudProcuraduria::route('/{record}'),
            'edit' => Pages\EditSolicitudProcuraduria::route('/{record}/edit'),
        ];
    }
}
