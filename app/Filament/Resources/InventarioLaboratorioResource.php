<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\InventarioLaboratorio;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Support\HasCrudPermissions;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\InventarioLaboratorioResource\Pages;
use App\Filament\Resources\InventarioLaboratorioResource\RelationManagers;

class InventarioLaboratorioResource extends Resource
{
        use HasCrudPermissions;

        protected static string $permPrefix = 'inventario_laboratorio';

    protected static ?string $model = InventarioLaboratorio::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationGroup = 'Administración';

    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre_item')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('descripcion')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('cantidad_total')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('cantidad_disponible')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('ubicacion')
                    ->maxLength(255),
                Forms\Components\TextInput::make('estado_item')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('item_id') // <-- Se ha agregado esta columna
                    ->label('ID de Item')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('nombre_item')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cantidad_total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cantidad_disponible')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ubicacion')
                    ->searchable(),
                Tables\Columns\TextColumn::make('estado_item')
                    ->searchable(),
            ])
            ->filters([
                //
        ])->actions([
            Tables\Actions\ViewAction::make()->visible(fn() => auth()->user()?->tienePermiso('inventario_laboratorio.ver') ?? false),
            Tables\Actions\EditAction::make()->visible(fn() => auth()->user()?->tienePermiso('inventario_laboratorio.editar') ?? false),
            Tables\Actions\DeleteAction::make()->visible(fn() => auth()->user()?->tienePermiso('inventario_laboratorio.eliminar') ?? false),
        ])->bulkActions([
            Tables\Actions\DeleteBulkAction::make()->visible(fn() => auth()->user()?->tienePermiso('inventario_laboratorio.eliminar') ?? false),
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
            'index' => Pages\ListInventarioLaboratorios::route('/'),
            'create' => Pages\CreateInventarioLaboratorio::route('/create'),
            'view' => Pages\ViewInventarioLaboratorio::route('/{record}'),
            'edit' => Pages\EditInventarioLaboratorio::route('/{record}/edit'),
        ];
    }
}
