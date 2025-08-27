<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\LoteProduccion;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Support\HasCrudPermissions;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\LoteProduccionResource\Pages;
use App\Filament\Resources\LoteProduccionResource\RelationManagers;

class LoteProduccionResource extends Resource
{
        use HasCrudPermissions;

        protected static string $permPrefix = 'lote_produccion';
    protected static ?string $model = LoteProduccion::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

        protected static ?string $navigationGroup = 'Cultivo';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('cepa_id')
                    ->relationship('cepa', 'cepa_id')
                    ->required(),
                Forms\Components\Select::make('lote_inoculo_id')
                    ->relationship('loteInoculo', 'lote_inoculo_id')
                    ->required(),
                Forms\Components\Select::make('proceso_esterilizacion_id')
                    ->relationship('procesoEsterilizacion', 'proceso_id')
                    ->required(),
                Forms\Components\TextInput::make('sala_id')
                    ->numeric(),
                Forms\Components\Select::make('usuario_creador_id')
                    ->relationship('usuarioCreador', 'usuario_id')
                    ->required(),
                Forms\Components\DatePicker::make('fecha_creacion_lote'),
                Forms\Components\Textarea::make('metodologia_inoculacion')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('notas_generales_lote')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('cepa.cepa_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('loteInoculo.lote_inoculo_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('procesoEsterilizacion.proceso_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sala_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('usuarioCreador.usuario_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_creacion_lote')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                //
        ])->actions([
            Tables\Actions\ViewAction::make()->visible(fn() => auth()->user()?->tienePermiso('lote_produccion.ver') ?? false),
            Tables\Actions\EditAction::make()->visible(fn() => auth()->user()?->tienePermiso('lote_produccion.editar') ?? false),
            Tables\Actions\DeleteAction::make()->visible(fn() => auth()->user()?->tienePermiso('lote_produccion.eliminar') ?? false),
        ])->bulkActions([
            Tables\Actions\DeleteBulkAction::make()->visible(fn() => auth()->user()?->tienePermiso('lote_produccion.eliminar') ?? false),
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
            'index' => Pages\ListLoteProduccions::route('/'),
            'create' => Pages\CreateLoteProduccion::route('/create'),
            'view' => Pages\ViewLoteProduccion::route('/{record}'),
            'edit' => Pages\EditLoteProduccion::route('/{record}/edit'),
        ];
    }
}
