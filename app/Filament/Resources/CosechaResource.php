<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Cosecha;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Support\HasCrudPermissions;

use App\Filament\Resources\CosechaResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CosechaResource\RelationManagers;

class CosechaResource extends Resource
{
    use HasCrudPermissions;
    protected static ?string $model = Cosecha::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

        protected static ?string $navigationGroup = 'Cultivo';

        protected static string $permPrefix = 'cosecha';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('unidad_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('numero_cosecha')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('fecha_cosecha')
                    ->required(),
                Forms\Components\TextInput::make('peso_cosecha_gramos')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('eficiencia_biologica_calculada')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('unidad_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('numero_cosecha')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_cosecha')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('peso_cosecha_gramos')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('eficiencia_biologica_calculada')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
        ])->actions([
            Tables\Actions\ViewAction::make()->visible(fn() => auth()->user()?->tienePermiso('cosecha.ver') ?? false),
            Tables\Actions\EditAction::make()->visible(fn() => auth()->user()?->tienePermiso('cosecha.editar') ?? false),
            Tables\Actions\DeleteAction::make()->visible(fn() => auth()->user()?->tienePermiso('cosecha.eliminar') ?? false),
        ])->bulkActions([
            Tables\Actions\DeleteBulkAction::make()->visible(fn() => auth()->user()?->tienePermiso('cosecha.eliminar') ?? false),
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
            'index' => Pages\ListCosechas::route('/'),
            'create' => Pages\CreateCosecha::route('/create'),
            'view' => Pages\ViewCosecha::route('/{record}'),
            'edit' => Pages\EditCosecha::route('/{record}/edit'),
        ];
    }
}
