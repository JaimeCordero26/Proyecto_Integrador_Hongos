<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\BitacoraActividad;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Support\HasCrudPermissions;

use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BitacoraActividadResource\Pages;
use App\Filament\Resources\BitacoraActividadResource\RelationManagers;
use App\Models\Usuario; // Asegúrate de importar el modelo

class BitacoraActividadResource extends Resource
{
        use HasCrudPermissions;

    protected static string $permPrefix = 'bitacora_actividad';
    protected static ?string $model = BitacoraActividad::class;
    protected static ?string $pluralModelLabel = 'Bitácora de actividades'; 

    protected static ?string $navigationLabel = 'Bitacora actividades';
    protected static ?string $plural = '';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Administración';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('usuario_id')
                    ->relationship('usuario', 'nombre_completo') // <-- Ahora muestra el nombre completo
                    ->required(),
                Forms\Components\DateTimePicker::make('fecha_hora'),
                Forms\Components\TextInput::make('tipo_actividad')
                    ->maxLength(255),
                Forms\Components\Textarea::make('descripcion_detallada')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('usuario.nombre_completo') // <-- Muestra el nombre en la tabla
                    ->label('Usuario')
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_hora')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tipo_actividad')
                    ->searchable(),
            ])
            ->filters([
                //
        ])->actions([
            Tables\Actions\ViewAction::make()->visible(fn() => auth()->user()?->tienePermiso('bitacora_actividad.ver') ?? false),
            Tables\Actions\EditAction::make()->visible(fn() => auth()->user()?->tienePermiso('bitacora_actividad.editar') ?? false),
            Tables\Actions\DeleteAction::make()->visible(fn() => auth()->user()?->tienePermiso('bitacora_actividad.eliminar') ?? false),
        ])->bulkActions([
            Tables\Actions\DeleteBulkAction::make()->visible(fn() => auth()->user()?->tienePermiso('bitacora_actividad.eliminar') ?? false),
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
            'index' => Pages\ListBitacoraActividads::route('/'),
            'create' => Pages\CreateBitacoraActividad::route('/create'),
            'view' => Pages\ViewBitacoraActividad::route('/{record}'),
            'edit' => Pages\EditBitacoraActividad::route('/{record}/edit'),
        ];
    }
}
