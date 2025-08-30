<?php
namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\RegistroAmbiental;
use App\Services\ReporteGenerador;
use Illuminate\Database\Eloquent\Collection;
use App\Filament\Support\HasCrudPermissions;
use App\Filament\Resources\RegistroAmbientalResource\Pages;
use App\Filament\Resources\RegistroAmbientalResource\RelationManagers;

class RegistroAmbientalResource extends Resource
{
    use HasCrudPermissions;

    protected static string $permPrefix = 'registro_ambiental';
    protected static ?string $navigationLabel = 'Registros Ambientales';
    protected static ?string $model = RegistroAmbiental::class;
    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationGroup = 'Cultivo';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('sala_id')
                    ->relationship('salaCultivo', 'nombre_sala')
                    ->required(),
                Forms\Components\DateTimePicker::make('fecha_hora'),
                Forms\Components\TextInput::make('temperatura_celsius')->numeric(),
                Forms\Components\TextInput::make('humedad_relativa')->numeric(),
                Forms\Components\TextInput::make('co2_ppm')->numeric(),
                Forms\Components\TextInput::make('luz_lm')->numeric()->label('Lúmenes'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sala_id')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('fecha_hora')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('temperatura_celsius')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('humedad_relativa')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('co2_ppm')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('luz_lm')->numeric()->label('Lúmenes')->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->visible(fn() => auth()->user()?->tienePermiso('registro_ambiental.ver') ?? false),

                Tables\Actions\EditAction::make()
                    ->visible(fn() => auth()->user()?->tienePermiso('registro_ambiental.editar') ?? false),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn() => auth()->user()?->tienePermiso('registro_ambiental.eliminar') ?? false),

                Tables\Actions\Action::make('generar_reporte')
                    ->label('Reporte')
                    ->icon('heroicon-o-printer')
                    ->action(function (RegistroAmbiental $record) {
                        try {
                            $reporteGenerador = new ReporteGenerador();

                            $downloadUrl = $reporteGenerador->generarReporte(
                                registros: collect([$record]),
                                titulo: 'Reporte Ambiental',
                                columnas: [
                                    'fecha_hora' => 'Fecha y Hora',
                                    'temperatura_celsius' => 'Temperatura (C)',
                                    'humedad_relativa' => 'Humedad (%)',
                                    'co2_ppm' => 'CO2 (ppm)',
                                    'luz_lm' => 'Lumenes'
                                ],
                                nombreArchivo: 'reporte_ambiental_' . $record->registro_id . '.pdf'
                            );

                            \Filament\Notifications\Notification::make()
                                ->title('Reporte generado exitosamente')
                                ->body('El reporte se ha creado correctamente.')
                                ->success()
                                ->actions([
                                    \Filament\Notifications\Actions\Action::make('download')
                                        ->label('Descargar PDF')
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
                    ->color('success')
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->visible(fn() => auth()->user()?->tienePermiso('registro_ambiental.eliminar') ?? false),

                Tables\Actions\BulkAction::make('generar_reporte_bulk')
                    ->label('Generar PDFs')
                    ->icon('heroicon-o-printer')
                    ->requiresConfirmation()
                    ->modalHeading('Generar Reporte Ambiental')
                    ->modalDescription(function (Collection $records) {
                        $count = $records->count();
                        $limit = 100;

                        if ($count > $limit) {
                            return "⚠️ ADVERTENCIA: Has seleccionado {$count} registros.
                            Para evitar problemas de memoria, se procesarán solo los primeros {$limit} registros.
                            Te recomendamos usar filtros para reducir la selección.";
                        }

                        return "Se generará un PDF con {$count} registros ambientales seleccionados, agrupados por Sala.";
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
                                titulo: 'Reporte Ambiental',
                                columnas: [
                                    'fecha_hora' => 'Fecha y Hora',
                                    'temperatura_celsius' => 'Temperatura (°C)',
                                    'humedad_relativa' => 'Humedad (%)',
                                    'co2_ppm' => 'CO₂ (ppm)',
                                    'luz_lm' => 'Lúmenes',
                                ],
                                nombreArchivo: 'reportes_ambientales_' . date('Y-m-d_H-i-s') . '.pdf',
                                groupBy: 'sala_id',
                                opciones: [
                                    'grupo_titulo' => 'Sala',
                                    'salto_pagina_grupos' => true,
                                ]
                            );

                            \Filament\Notifications\Notification::make()
                                ->title('Reporte generado exitosamente')
                                ->body('Se procesaron ' . $records->count() . ' registros.' .
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
                            \Log::error('Error generando reportes ambientales: ' . $e->getMessage());

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
        return [

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
