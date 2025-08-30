<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UsuarioResource\Pages;
use App\Models\Usuario;
use App\Models\Rol;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Password as PasswordRule;
use App\Services\ReporteGenerador;
use Illuminate\Support\Collection;

class UsuarioResource extends Resource
{
    protected static ?string $model = Usuario::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Usuarios';
    protected static ?string $modelLabel = 'Usuario';
    protected static ?string $pluralModelLabel = 'Usuarios';
    protected static ?string $navigationGroup = 'Seguridad';

    public static function canViewAny(): bool
    {
        return auth()->user()?->tienePermiso('usuarios.ver') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('rol_id')
                ->label('Rol')
                ->options(Rol::query()->pluck('nombre_rol','rol_id'))
                ->searchable()
                ->preload()
                ->required(),

            Forms\Components\TextInput::make('nombre_completo')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('email')
                ->email()
                ->required()
                ->unique(ignoreRecord: true, column: 'email'),

            Forms\Components\TextInput::make('password')
                ->password()
                ->revealable()
                ->required(fn (string $context) => $context === 'create')
                ->dehydrated(fn (?string $state) => filled($state))
                ->dehydrateStateUsing(fn (string $state) => $state)
                ->rules([PasswordRule::min(8)]),

            Forms\Components\Toggle::make('activo')
                ->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('rol.nombre_rol')
                ->label('Rol')
                ->badge()
                ->sortable()
                ->toggleable(),

            Tables\Columns\TextColumn::make('nombre_completo')
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('email')
                ->searchable()
                ->sortable(),

            Tables\Columns\IconColumn::make('activo')
                ->boolean()
                ->sortable(),
        ])->actions([
            Tables\Actions\ViewAction::make()
                ->visible(fn() => auth()->user()?->tienePermiso('usuarios.ver') ?? false),

            Tables\Actions\EditAction::make()
                ->visible(fn() => auth()->user()?->tienePermiso('usuarios.editar') ?? false),

            Tables\Actions\DeleteAction::make()
                ->visible(fn() => auth()->user()?->tienePermiso('usuarios.eliminar') ?? false),

            Tables\Actions\Action::make('generar_reporte')
                ->label('Generar PDF')
                ->icon('heroicon-o-printer')
                ->action(function (Usuario $record) {
                    try {
                        $reporteGenerador = new ReporteGenerador();
                        $downloadUrl = $reporteGenerador->generarReporte(
                            registros: collect([$record]),
                            titulo: 'Reporte de Usuario',
                            columnas: [
                                'rol_nombre' => 'Rol',
                                'nombre_completo' => 'Nombre Completo',
                                'email' => 'Correo Electrónico',
                                'activo_texto' => 'Estado',
                            ],
                            nombreArchivo: 'reporte_usuario_' . $record->usuario_id . '.pdf'
                        );

                        \Filament\Notifications\Notification::make()
                            ->title('Reporte generado exitosamente')
                            ->body('El PDF del usuario se ha creado correctamente.')
                            ->success()
                            ->actions([
                                \Filament\Notifications\Actions\Action::make('download')
                                    ->label('Ver PDF')
                                    ->url($downloadUrl)
                                    ->openUrlInNewTab()
                                    ->button()
                            ])
                            ->duration(15000)
                            ->send();
                    } catch (\Exception $e) {
                        \Log::error('Error generando reporte de usuario: ' . $e->getMessage());
                        \Filament\Notifications\Notification::make()
                            ->title('Error al generar reporte')
                            ->body('Error: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
                ->color('success'),
        ])->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->visible(fn() => auth()->user()?->tienePermiso('usuarios.eliminar') ?? false),

                Tables\Actions\BulkAction::make('generar_reporte_bulk')
                    ->label('Generar PDFs')
                    ->icon('heroicon-o-printer')
                    ->requiresConfirmation()
                    ->modalHeading('Generar Reporte de Usuarios')
                    ->modalDescription(function (Collection $records) {
                        $count = $records->count();
                        $limit = 100;

                        if ($count > $limit) {
                            return "⚠️ ADVERTENCIA: Has seleccionado {$count} usuarios. Para evitar problemas de memoria, se procesarán solo los primeros {$limit} usuarios. Te recomendamos usar filtros para reducir la selección.";
                        }

                        return "Se generará un PDF con {$count} usuarios seleccionados.";
                    })
                    ->modalSubmitActionLabel('Generar PDF')
                    ->action(function (Collection $records) {
                        $limit = 100;
                        $originalCount = $records->count();

                        if ($originalCount > $limit) {
                            $records = $records->take($limit);

                            \Filament\Notifications\Notification::make()
                                ->title('Información')
                                ->body("Se limitó el reporte a {$limit} usuarios de {$originalCount} seleccionados para evitar problemas de memoria.")
                                ->warning()
                                ->duration(5000)
                                ->send();
                        }

                        try {
                            $reporteGenerador = new ReporteGenerador();
                            $downloadUrl = $reporteGenerador->generarReporte(
                                registros: $records,
                                titulo: 'Reporte de Usuarios',
                                columnas: [
                                    'rol_nombre' => 'Rol',
                                    'nombre_completo' => 'Nombre Completo',
                                    'email' => 'Correo Electrónico',
                                    'activo_texto' => 'Estado',
                                ],
                                nombreArchivo: 'reportes_usuarios_' . date('Y-m-d_H-i-s') . '.pdf'
                            );

                            \Filament\Notifications\Notification::make()
                                ->title('Reportes generados exitosamente')
                                ->body('Se procesaron ' . $records->count() . ' usuarios.' .
                                    ($originalCount > $limit ? " (Limitado de {$originalCount} usuarios)" : ''))
                                ->success()
                                ->actions([
                                    \Filament\Notifications\Actions\Action::make('download')
                                        ->label('Ver PDFs')
                                        ->url($downloadUrl)
                                        ->openUrlInNewTab()
                                        ->button()
                                ])
                                ->duration(15000)
                                ->send();
                        } catch (\Exception $e) {
                            \Log::error('Error generando reportes de usuario: ' . $e->getMessage());
                            \Filament\Notifications\Notification::make()
                                ->title('Error al generar reportes')
                                ->body('Error: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->color('success')
                    ->visible(fn() => auth()->user()?->tienePermiso('usuarios.ver') ?? false),
                ]);

    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsuarios::route('/'),
            'create' => Pages\CreateUsuario::route('/create'),
            'edit' => Pages\EditUsuario::route('/{record}/edit'),
        ];
    }
}
