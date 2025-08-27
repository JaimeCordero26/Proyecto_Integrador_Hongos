<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\UnidadProduccion;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Support\HasCrudPermissions;

use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UnidadProduccionResource\Pages;
use App\Filament\Resources\UnidadProduccionResource\RelationManagers;

class UnidadProduccionResource extends Resource
{
        use HasCrudPermissions;

        protected static string $permPrefix = 'unidad_produccion';

    protected static ?string $model = UnidadProduccion::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

        protected static ?string $navigationGroup = 'Cultivo';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('lote_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('codigo_unidad')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('peso_inicial_gramos')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('fecha_inoculacion')
                    ->required(),
                Forms\Components\TextInput::make('estado_unidad')
                    ->maxLength(255),
                Forms\Components\Select::make('tipo_contaminacion_id')
                    ->relationship('tipoContaminacion', 'tipo_contaminacion_id'),
                Forms\Components\Textarea::make('notas_contaminacion')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('lote_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('codigo_unidad')
                    ->searchable(),
                Tables\Columns\TextColumn::make('peso_inicial_gramos')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_inoculacion')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('estado_unidad')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipoContaminacion.tipo_contaminacion_id')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
        ])->actions([
            Tables\Actions\ViewAction::make()->visible(fn() => auth()->user()?->tienePermiso('unidad_produccion.ver') ?? false),
            Tables\Actions\EditAction::make()->visible(fn() => auth()->user()?->tienePermiso('unidad_produccion.editar') ?? false),
            Tables\Actions\DeleteAction::make()->visible(fn() => auth()->user()?->tienePermiso('unidad_produccion.eliminar') ?? false),
        ])->bulkActions([
            Tables\Actions\DeleteBulkAction::make()->visible(fn() => auth()->user()?->tienePermiso('unidad_produccion.eliminar') ?? false),
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
            'index' => Pages\ListUnidadProduccions::route('/'),
            'create' => Pages\CreateUnidadProduccion::route('/create'),
            'view' => Pages\ViewUnidadProduccion::route('/{record}'),
            'edit' => Pages\EditUnidadProduccion::route('/{record}/edit'),
        ];
    }
}
