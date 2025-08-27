<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\SalaCultivo;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Support\HasCrudPermissions;

use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SalaCultivoResource\Pages;
use App\Filament\Resources\SalaCultivoResource\RelationManagers;

class SalaCultivoResource extends Resource
{
        use HasCrudPermissions;

        protected static string $permPrefix = 'sala_cultivo';

    protected static ?string $model = SalaCultivo::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationGroup = 'Administración';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre_sala')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('descripcion')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('proposito')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre_sala')
                    ->searchable(),
                Tables\Columns\TextColumn::make('proposito')
                    ->searchable(),
            ])
            ->filters([
                //
        ])->actions([
            Tables\Actions\ViewAction::make()->visible(fn() => auth()->user()?->tienePermiso('sala_cultivo.ver') ?? false),
            Tables\Actions\EditAction::make()->visible(fn() => auth()->user()?->tienePermiso('sala_cultivo.editar') ?? false),
            Tables\Actions\DeleteAction::make()->visible(fn() => auth()->user()?->tienePermiso('sala_cultivo.eliminar') ?? false),
        ])->bulkActions([
            Tables\Actions\DeleteBulkAction::make()->visible(fn() => auth()->user()?->tienePermiso('sala_cultivo.eliminar') ?? false),
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
            'index' => Pages\ListSalaCultivos::route('/'),
            'create' => Pages\CreateSalaCultivo::route('/create'),
            'view' => Pages\ViewSalaCultivo::route('/{record}'),
            'edit' => Pages\EditSalaCultivo::route('/{record}/edit'),
        ];
    }
}
