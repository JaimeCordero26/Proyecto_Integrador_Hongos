<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\ProcesoEsterilizacion;
use App\Filament\Support\HasCrudPermissions;
use Illuminate\Database\Eloquent\Collection;
use App\Services\ReporteGenerador;
use App\Filament\Resources\ProcesoEsterilizacionResource\Pages;

class ProcesoEsterilizacionResource extends Resource
{
    use HasCrudPermissions;

    protected static string $permPrefix = 'proceso_esterilizacion';
    protected static ?string $navigationGroup = 'Administración';
    protected static ?string $model = ProcesoEsterilizacion::class;
    protected static ?string $pluralModelLabel = "Procesos de Esterilización";
    protected static ?string $navigationLabel = "Procesos de Esterilización";
    protected static ?string $navigationIcon = 'heroicon-o-fire';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('metodo')
                    ->label('Método')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('temperatura_alcanzada_c')
                    ->label('Temperatura (°C)')
                    ->numeric()
                    ->step(0.01)
                    ->suffix('°C'),
                Forms\Components\TextInput::make('presion_psi')
                    ->label('Presión (PSI)')
                    ->numeric()
                    ->step(0.01)
                    ->suffix('PSI'),
                Forms\Components\TextInput::make('duracion_minutos')
                    ->label('Duración (minutos)')
                    ->required()
                    ->numeric()
                    ->suffix('min'),
                Forms\Components\Textarea::make('descripcion_adicional')
                    ->label('Descripción Adicional')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('metodo')
                    ->label('Método')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('temperatura_alcanzada_c')
                    ->label('Temperatura (°C)')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 1) . '°C' : 'N/A'),
                Tables\Columns\TextColumn::make('presion_psi')
                    ->label('Presión (PSI)')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 1) . ' PSI' : 'N/A'),
                Tables\Columns\TextColumn::make('duracion_minutos')
                    ->label('Duración (min)')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state . ' min'),
            ])
            ->defaultSort('metodo', 'asc')
            ->filters([
                Tables\Filters\Filter::make('con_temperatura')
                    ->label('Con Temperatura')
                    ->query(fn ($query) => $query->whereNotNull('temperatura_alcanzada_c')),
                Tables\Filters\Filter::make('con_presion')
                    ->label('Con Presión')
                    ->query(fn ($query) => $query->whereNotNull('presion_psi')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->visible(fn() => auth()->user()?->tienePermiso('proceso_esterilizacion.ver') ?? false),
                Tables\Actions\EditAction::make()
                    ->visible(fn() => auth()->user()?->tienePermiso('proceso_esterilizacion.editar') ?? false),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn() => auth()->user()?->tienePermiso('proceso_esterilizacion.eliminar') ?? false),

                Tables\Actions\Action::make('generar_reporte')
                    ->label('Generar Reporte')
                    ->icon('heroicon-o-printer')
                    ->action(function (ProcesoEsterilizacion $record) {
                        try {
                            $reporteGenerador = new ReporteGenerador();
                            $downloadUrl = $reporteGenerador->generarReporte(
                                registros: collect([$record]),
                                titulo: 'Reporte de Proceso de Esterilización',
                                columnas: [
                                    'metodo' => 'Método',
                                    'temperatura_alcanzada_c' => 'Temperatura (°C)',
                                    'presion_psi' => 'Presión (PSI)',
                                    'duracion_minutos' => 'Duración (min)',
                                    'descripcion_adicional' => 'Descripción Adicional',
                                ],
                                nombreArchivo: 'reporte_proceso_esterilizacion_' . $record->proceso_id . '.pdf'
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
                    ->visible(fn() => auth()->user()?->tienePermiso('proceso_esterilizacion.eliminar') ?? false),

                Tables\Actions\BulkAction::make('generar_reporte_bulk')
                    ->label('Generar PDFs')
                    ->icon('heroicon-o-printer')
                    ->requiresConfirmation()
                    ->modalHeading('Generar Reporte de Procesos de Esterilización')
                    ->modalDescription(function (Collection $records) {
                        $count = $records->count();
                        $limit = 100;

                        if ($count > $limit) {
                            return "⚠️ ADVERTENCIA: Has seleccionado {$count} registros.
                            Para evitar problemas de memoria, se procesarán solo los primeros {$limit} registros.
                            Te recomendamos usar filtros para reducir la selección.";
                        }

                        return "Se generará un PDF con {$count} procesos seleccionados.";
                    })
                    ->modalSubmitActionLabel('Generar PDF')
                    ->action(function (Collection $records) {
                        $limit = 100;
                        $originalCount = $records->count();

                        if ($originalCount > $limit) {
                            $records = $records->take($limit);

                            \Filament\Notifications\Notification::make()
                                ->title('Información')
                                ->body("Se limitó el reporte a {$limit} registros de {$originalCount} seleccionados para evitar problemas de memoria.")
                                ->warning()
                                ->duration(5000)
                                ->send();
                        }

                        try {
                            $reporteGenerador = new \App\Services\ReporteGenerador();

                            $downloadUrl = $reporteGenerador->generarReporte(
                                registros: $records,
                                titulo: 'Reporte de Procesos de Esterilización',
                                columnas: [
                                    'metodo' => 'Método',
                                    'temperatura_alcanzada_c' => 'Temperatura (°C)',
                                    'presion_psi' => 'Presión (PSI)',
                                    'duracion_minutos' => 'Duración (min)',
                                    'descripcion_adicional' => 'Descripción Adicional',
                                ],
                                nombreArchivo: 'reportes_procesos_esterilizacion_' . date('Y-m-d_H-i-s') . '.pdf'
                            );

                            \Filament\Notifications\Notification::make()
                                ->title('Reportes generados exitosamente')
                                ->body('Se procesaron ' . $records->count() . ' procesos.' .
                                    ($originalCount > $limit ? " (Limitado de {$originalCount} registros)" : ''))
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
                            \Log::error('Error generando reportes de procesos: ' . $e->getMessage());
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
            'index' => Pages\ListProcesoEsterilizacions::route('/'),
            'create' => Pages\CreateProcesoEsterilizacion::route('/create'),
            'view' => Pages\ViewProcesoEsterilizacion::route('/{record}'),
            'edit' => Pages\EditProcesoEsterilizacion::route('/{record}/edit'),
        ];
    }
}
