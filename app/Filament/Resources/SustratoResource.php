<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Sustrato;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Support\HasCrudPermissions;
use App\Filament\Resources\SustratoResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SustratoResource\RelationManagers;

class SustratoResource extends Resource
{
        use HasCrudPermissions;

        protected static string $permPrefix = 'sustrato';
    protected static ?string $model = Sustrato::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

        protected static ?string $navigationGroup = 'Cultivo';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre_sustrato')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('descripcion')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre_sustrato')
                    ->searchable(),
            ])
            ->filters([
                //
        ])->actions([
            Tables\Actions\ViewAction::make()->visible(fn() => auth()->user()?->tienePermiso('sustrato.ver') ?? false),
            Tables\Actions\EditAction::make()->visible(fn() => auth()->user()?->tienePermiso('sustrato.editar') ?? false),
            Tables\Actions\DeleteAction::make()->visible(fn() => auth()->user()?->tienePermiso('sustrato.eliminar') ?? false),
        ])->bulkActions([
            Tables\Actions\DeleteBulkAction::make()->visible(fn() => auth()->user()?->tienePermiso('sustrato.eliminar') ?? false),
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
            'index' => Pages\ListSustratos::route('/'),
            'create' => Pages\CreateSustrato::route('/create'),
            'view' => Pages\ViewSustrato::route('/{record}'),
            'edit' => Pages\EditSustrato::route('/{record}/edit'),
        ];
    }
}
