<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\LoteInoculo;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Support\HasCrudPermissions;
use Illuminate\DatabaseEloquent\SoftDeletingScope;
use App\Filament\Resources\LoteInoculoResource\Pages;

class LoteInoculoResource extends Resource
{
        use HasCrudPermissions;
    protected static ?string $model = LoteInoculo::class;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';

        protected static ?string $navigationGroup = 'Cultivo';

        protected static string $permPrefix = 'lote_inoculo';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('cepa_id')
                    ->relationship('cepa', 'cepa_id')
                    ->required(),
                Forms\Components\Select::make('usuario_creador_id')
                    ->relationship('usuarioCreador', 'usuario_id')
                    ->required(),
                Forms\Components\DatePicker::make('fecha_creacion')
                    ->required(),
                Forms\Components\TextInput::make('sustrato_grano')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('generacion')
                    ->maxLength(255),
                Forms\Components\Select::make('proceso_esterilizacion_id')
                    ->relationship('procesoEsterilizacion', 'proceso_id'),
                Forms\Components\Textarea::make('notas')
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
                Tables\Columns\TextColumn::make('usuarioCreador.usuario_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_creacion')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sustrato_grano')
                    ->searchable(),
                Tables\Columns\TextColumn::make('generacion')
                    ->searchable(),
                Tables\Columns\TextColumn::make('procesoEsterilizacion.proceso_id')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
        ])->actions([
            Tables\Actions\ViewAction::make()->visible(fn() => auth()->user()?->tienePermiso('lote_inoculo.ver') ?? false),
            Tables\Actions\EditAction::make()->visible(fn() => auth()->user()?->tienePermiso('lote_inoculo.editar') ?? false),
            Tables\Actions\DeleteAction::make()->visible(fn() => auth()->user()?->tienePermiso('lote_inoculo.eliminar') ?? false),
        ])->bulkActions([
            Tables\Actions\DeleteBulkAction::make()->visible(fn() => auth()->user()?->tienePermiso('lote_inoculo.eliminar') ?? false),
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
            'index' => Pages\ListLoteInoculos::route('/'),
            'create' => Pages\CreateLoteInoculo::route('/create'),
            'view' => Pages\ViewLoteInoculo::route('/{record}'),
            'edit' => Pages\EditLoteInoculo::route('/{record}/edit'),
        ];
    }
}
