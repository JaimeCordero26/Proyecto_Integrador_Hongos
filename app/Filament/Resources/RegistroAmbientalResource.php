<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\RegistroAmbiental;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Support\HasCrudPermissions;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\RegistroAmbientalResource\Pages;
use App\Filament\Resources\RegistroAmbientalResource\RelationManagers;

class RegistroAmbientalResource extends Resource
{
        use HasCrudPermissions;

        protected static string $permPrefix = 'registro_ambiental';
        protected static ?string $navigationLabel = 'Registros ambientales';
    protected static ?string $model = RegistroAmbiental::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

        protected static ?string $navigationGroup = 'Cultivo';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('sala_id')
                    ->required()
                    ->numeric(),
                Forms\Components\DateTimePicker::make('fecha_hora'),
                Forms\Components\TextInput::make('temperatura_celsius')
                    ->numeric(),
                Forms\Components\TextInput::make('humedad_relativa')
                    ->numeric(),
                Forms\Components\TextInput::make('co2_ppm')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sala_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_hora')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('temperatura_celsius')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('humedad_relativa')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('co2_ppm')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
        ])->actions([
            Tables\Actions\ViewAction::make()->visible(fn() => auth()->user()?->tienePermiso('registro_ambiental.ver') ?? false),
            Tables\Actions\EditAction::make()->visible(fn() => auth()->user()?->tienePermiso('registro_ambiental.editar') ?? false),
            Tables\Actions\DeleteAction::make()->visible(fn() => auth()->user()?->tienePermiso('registro_ambiental.eliminar') ?? false),
        ])->bulkActions([
            Tables\Actions\DeleteBulkAction::make()->visible(fn() => auth()->user()?->tienePermiso('registro_ambiental.eliminar') ?? false),
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
            'index' => Pages\ListRegistroAmbientals::route('/'),
            'create' => Pages\CreateRegistroAmbiental::route('/create'),
            'view' => Pages\ViewRegistroAmbiental::route('/{record}'),
            'edit' => Pages\EditRegistroAmbiental::route('/{record}/edit'),
        ];
    }
}
