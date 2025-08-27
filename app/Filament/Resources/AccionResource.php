<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AccionResource\Pages;
use App\Filament\Resources\AccionResource\RelationManagers;
use App\Models\Accion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Support\HasCrudPermissions;
use Illuminate\Database\Eloquent\Builder;

class AccionResource extends Resource
{
    use HasCrudPermissions;

    protected static string $permPrefix = 'accion';

        protected static ?string $navigationGroup = 'Auditoría';


    protected static ?string $model = Accion::class;
    protected static ?string $navigationLabel = "Acciones";
    protected static ?string $modelLabel = "Acciones";
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('tabla_afectada')
                    ->maxLength(100),
                Forms\Components\TextInput::make('id_registro')
                    ->numeric(),
                Forms\Components\TextInput::make('tipo_accion')
                    ->maxLength(10),
                Forms\Components\TextInput::make('usuario_id')
                    ->numeric(),
                Forms\Components\DateTimePicker::make('fecha_hora'),
                Forms\Components\Textarea::make('descripcion')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tabla_afectada')
                    ->searchable(),
                Tables\Columns\TextColumn::make('id_registro')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tipo_accion')
                    ->searchable(),
                Tables\Columns\TextColumn::make('usuario_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_hora')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
        ])->actions([
            Tables\Actions\ViewAction::make()->visible(fn() => auth()->user()?->tienePermiso('accion.ver') ?? false),
            Tables\Actions\EditAction::make()->visible(fn() => auth()->user()?->tienePermiso('accion.editar') ?? false),
            Tables\Actions\DeleteAction::make()->visible(fn() => auth()->user()?->tienePermiso('accion.eliminar') ?? false),
        ])->bulkActions([
            Tables\Actions\DeleteBulkAction::make()->visible(fn() => auth()->user()?->tienePermiso('accion.eliminar') ?? false),
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
            'index' => Pages\ListAccions::route('/'),
            'create' => Pages\CreateAccion::route('/create'),
            'view' => Pages\ViewAccion::route('/{record}'),
            'edit' => Pages\EditAccion::route('/{record}/edit'),
        ];
    }
}
