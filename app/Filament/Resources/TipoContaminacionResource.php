<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\TipoContaminacion;
use Illuminate\Database\Eloquent\Collection;
use App\Services\ReporteGenerador;
use App\Filament\Support\HasCrudPermissions;
use App\Filament\Resources\TipoContaminacionResource\Pages;

class TipoContaminacionResource extends Resource
{
    use HasCrudPermissions;

    protected static string $permPrefix = 'tipo_contaminacion';
    protected static ?string $model = TipoContaminacion::class;
    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';
    protected static ?string $navigationGroup = 'Cultivo';
    protected static ?string $navigationLabel = 'Tipos de Contaminación';
    protected static ?string $pluralModelLabel = 'Tipos de Contaminación';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre_comun')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('agente_causal')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre_comun')
                    ->searchable(),
                Tables\Columns\TextColumn::make('agente_causal')
                    ->searchable(),
            ])
            ->filters([

            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->visible(fn() => auth()->user()?->tienePermiso('tipo_contaminacion.ver') ?? false),
                Tables\Actions\EditAction::make()
                    ->visible(fn() => auth()->user()?->tienePermiso('tipo_contaminacion.editar') ?? false),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn() => auth()->user()?->tienePermiso('tipo_contaminacion.eliminar') ?? false),

                Tables\Actions\Action::make('generar_reporte')
                    ->label('Generar Reporte')
                    ->icon('heroicon-o-printer')
                    ->action(function (TipoContaminacion $record) {
                        try {
                            $reporteGenerador = new ReporteGenerador();
                            $downloadUrl = $reporteGenerador->generarReporte(
                                registros: collect([$record]),
                                titulo: 'Reporte de Tipo de Contaminación',
                                columnas: [
                                    'nombre_comun' => 'Nombre Común',
                                    'agente_causal' => 'Agente Causal',
                                ],
                                nombreArchivo: 'reporte_tipo_contaminacion_' . $record->id . '.pdf'
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
                    ->visible(fn() => auth()->user()?->tienePermiso('tipo_contaminacion.eliminar') ?? false),

                Tables\Actions\BulkAction::make('generar_reporte_bulk')
                    ->label('Generar Reportes')
                    ->icon('heroicon-o-printer')
                    ->requiresConfirmation()
                    ->modalHeading('Generar Reporte de Tipos de Contaminación')
                    ->modalDescription(function (Collection $records) {
                        $count = $records->count();
                        $limit = 100;

                        if ($count > $limit) {
                            return "⚠️ ADVERTENCIA: Has seleccionado {$count} registros. Para evitar problemas de memoria, se procesarán solo los primeros {$limit} registros. Te recomendamos usar filtros para reducir la selección.";
                        }

                        return "Se generará un PDF con {$count} registros seleccionados.";
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
                            $reporteGenerador = new ReporteGenerador();
                            $downloadUrl = $reporteGenerador->generarReporte(
                                registros: $records,
                                titulo: 'Reporte de Tipos de Contaminación',
                                columnas: [
                                    'nombre_comun' => 'Nombre Común',
                                    'agente_causal' => 'Agente Causal',
                                ],
                                nombreArchivo: 'reportes_tipo_contaminacion_' . date('Y-m-d_H-i-s') . '.pdf'
                            );

                            \Filament\Notifications\Notification::make()
                                ->title('Reportes generados exitosamente')
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
                            \Log::error('Error generando reportes: ' . $e->getMessage());
                            \Filament\Notifications\Notification::make()
                                ->title('Error al generar reportes')
                                ->body('Error: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->color('success')
                    ->visible(fn() => auth()->user()?->tienePermiso('tipo_contaminacion.ver') ?? false),
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
            'index' => Pages\ListTipoContaminacions::route('/'),
            'create' => Pages\CreateTipoContaminacion::route('/create'),
            'view' => Pages\ViewTipoContaminacion::route('/{record}'),
            'edit' => Pages\EditTipoContaminacion::route('/{record}/edit'),
        ];
    }
}
