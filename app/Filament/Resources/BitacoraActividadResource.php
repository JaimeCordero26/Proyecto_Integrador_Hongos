<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\BitacoraActividad;
use App\Services\ReporteGenerador;
use Illuminate\Database\Eloquent\Collection;
use App\Filament\Support\HasCrudPermissions;
use App\Filament\Resources\BitacoraActividadResource\Pages;

class BitacoraActividadResource extends Resource
{
    use HasCrudPermissions;

    protected static string $permPrefix = 'bitacora_actividad';
    protected static ?string $model = BitacoraActividad::class;
    protected static ?string $pluralModelLabel = 'Bitácora de actividades';
    protected static ?string $navigationLabel = 'Bitácora Actividades';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Administración';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('usuario_id')
                    ->relationship('usuario', 'nombre_completo')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\DateTimePicker::make('fecha_hora')
                    ->required(),
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
                Tables\Columns\TextColumn::make('usuario.nombre_completo')
                    ->label('Usuario')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('fecha_hora')
                    ->label('Fecha y Hora')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tipo_actividad')
                    ->label('Tipo Actividad')
                    ->searchable(),
            ])
            ->defaultSort('fecha_hora', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('usuario_id')
                    ->relationship('usuario', 'nombre_completo')
                    ->label('Usuario'),
                Tables\Filters\SelectFilter::make('tipo_actividad')
                    ->options([
                        'Creación' => 'Creación',
                        'Edición' => 'Edición',
                        'Eliminación' => 'Eliminación',
                        'Consulta' => 'Consulta',
                    ])
                    ->label('Tipo de Actividad'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->visible(fn() => auth()->user()?->tienePermiso('bitacora_actividad.ver') ?? false),
                Tables\Actions\EditAction::make()
                    ->visible(fn() => auth()->user()?->tienePermiso('bitacora_actividad.editar') ?? false),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn() => auth()->user()?->tienePermiso('bitacora_actividad.eliminar') ?? false),

                // Acción individual para generar reporte
                Tables\Actions\Action::make('generar_reporte')
                    ->label('Generar Reporte')
                    ->icon('heroicon-o-printer')
                    ->action(function (BitacoraActividad $record) {
                        try {
                            $reporteGenerador = new ReporteGenerador();
                            $downloadUrl = $reporteGenerador->generarReporte(
                                registros: collect([$record]),
                                titulo: 'Reporte de Actividad',
                                columnas: [
                                    'usuario_nombre' => 'Usuario',
                                    'fecha_hora' => 'Fecha y Hora',
                                    'tipo_actividad' => 'Tipo de Actividad',
                                    'descripcion_detallada' => 'Descripción Detallada',
                                ],
                                nombreArchivo: 'reporte_bitacora_actividad_' . $record->id . '.pdf'
                            );

                            \Filament\Notifications\Notification::make()
                                ->title('Reporte generado exitosamente')
                                ->body('El reporte se ha creado correctamente.')
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
                            \Log::error('Error generando reporte: ' . $e->getMessage());
                            \Filament\Notifications\Notification::make()
                                ->title('Error al generar reporte')
                                ->body('Error: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->color('success'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->visible(fn() => auth()->user()?->tienePermiso('bitacora_actividad.eliminar') ?? false),

                // Acción bulk para generar reportes
                Tables\Actions\BulkAction::make('generar_reporte_bulk')
                    ->label('Generar Reportes')
                    ->icon('heroicon-o-printer')
                    ->action(function (Collection $records) {
                        try {
                            $reporteGenerador = new ReporteGenerador();
                            $downloadUrl = $reporteGenerador->generarReporte(
                                registros: $records,
                                titulo: 'Reporte de Actividades',
                                columnas: [
                                    'usuario_nombre' => 'Usuario',
                                    'fecha_hora' => 'Fecha y Hora',
                                    'tipo_actividad' => 'Tipo de Actividad',
                                    'descripcion_detallada' => 'Descripción Detallada',
                                ],
                                nombreArchivo: 'reportes_bitacora_actividades_' . date('Y-m-d_H-i-s') . '.pdf',
                                groupBy: 'usuario_nombre',
                                opciones: [
                                    'grupo_titulo' => 'Usuario',
                                    'salto_pagina_grupos' => true
                                ]
                            );

                            \Filament\Notifications\Notification::make()
                                ->title('Reportes generados exitosamente')
                                ->body('Se procesaron ' . $records->count() . ' registros.')
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
                            \Log::error('Error generando reportes: ' . $e->getMessage());
                            \Filament\Notifications\Notification::make()
                                ->title('Error al generar reportes')
                                ->body('Error: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->color('success'),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
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