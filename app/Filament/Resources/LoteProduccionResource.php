<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\LoteProduccion;
use Filament\Resources\Resource;
use App\Filament\Support\HasCrudPermissions;
use Illuminate\Database\Eloquent\Collection;
use App\Services\ReporteGenerador;
use App\Filament\Resources\LoteProduccionResource\Pages;

class LoteProduccionResource extends Resource
{
    use HasCrudPermissions;

    protected static string $permPrefix = 'lote_produccion';
    protected static ?string $navigationGroup = 'Cultivo';
    protected static ?string $model = LoteProduccion::class;
    protected static ?string $pluralModelLabel = "Lotes en Producción";
    protected static ?string $navigationLabel = "Lotes en Producción";
    protected static ?string $navigationIcon = 'heroicon-o-cube';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('cepa_id')
                    ->relationship('cepa', 'nombre_cientifico')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('lote_inoculo_id')
                    ->relationship('loteInoculo', 'lote_inoculo_id')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('proceso_esterilizacion_id')
                    ->relationship('procesoEsterilizacion', 'metodo')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('peso_sustrato_seco_kg')
                    ->label('Peso Sustrato (kg)')
                    ->numeric()
                    ->step(0.01)
                    ->suffix('kg')
                    ->required(),
                Forms\Components\Select::make('sala_id')
                    ->relationship('salaCultivo', 'nombre_sala')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('usuario_creador_id')
                    ->relationship('usuarioCreador', 'nombre_completo')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\DatePicker::make('fecha_creacion_lote')
                    ->required(),
                Forms\Components\Textarea::make('metodologia_inoculacion')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('notas_generales_lote')
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('lote_id')
                    ->label('ID Lote')
                    ->sortable()
                    ->numeric(),
                Tables\Columns\TextColumn::make('cepa.nombre_cientifico')
                    ->label('Cepa')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('loteInoculo.lote_inoculo_id')
                    ->label('Lote Inóculo ID')
                    ->sortable()
                    ->numeric(),
                Tables\Columns\TextColumn::make('procesoEsterilizacion.metodo')
                    ->label('Proceso Esterilización')
                    ->sortable()
                    ->searchable()
                    ->placeholder('Sin proceso'),
                Tables\Columns\TextColumn::make('peso_sustrato_seco_kg')
                    ->label('Peso Sustrato (kg)')
                    ->sortable()
                    ->numeric()
                    ->formatStateUsing(fn ($state) => number_format($state, 2) . ' kg'),
                Tables\Columns\TextColumn::make('salaCultivo.nombre_sala')
                    ->label('Sala')
                    ->sortable()
                    ->searchable()
                    ->placeholder('Sin sala'),
                Tables\Columns\TextColumn::make('usuarioCreador.nombre_completo')
                    ->label('Usuario Creador')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('fecha_creacion_lote')
                    ->label('Fecha Creación')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('fecha_creacion_lote', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('cepa_id')
                    ->relationship('cepa', 'nombre_cientifico')
                    ->label('Cepa'),
                Tables\Filters\SelectFilter::make('proceso_esterilizacion_id')
                    ->relationship('procesoEsterilizacion', 'metodo')
                    ->label('Proceso Esterilización'),
                Tables\Filters\SelectFilter::make('sala_id')
                    ->relationship('salaCultivo', 'nombre_sala')
                    ->label('Sala de Cultivo'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->visible(fn() => auth()->user()?->tienePermiso('lote_produccion.ver') ?? false),
                Tables\Actions\EditAction::make()
                    ->visible(fn() => auth()->user()?->tienePermiso('lote_produccion.editar') ?? false),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn() => auth()->user()?->tienePermiso('lote_produccion.eliminar') ?? false),

                // Acción individual para generar reporte
                Tables\Actions\Action::make('generar_reporte')
                    ->label('Generar Reporte')
                    ->icon('heroicon-o-printer')
                    ->action(function (LoteProduccion $record) {
                        try {
                            $reporteGenerador = new ReporteGenerador();
                            $downloadUrl = $reporteGenerador->generarReporte(
                                registros: collect([$record]),
                                titulo: 'Reporte de Lote en Producción',
                                columnas: [
                                    'lote_id' => 'ID Lote',
                                    'cepa_nombre' => 'Cepa',
                                    'lote_inoculo_id' => 'Lote Inóculo ID',
                                    'proceso_esterilizacion_nombre' => 'Proceso Esterilización',
                                    'peso_sustrato_seco_kg' => 'Peso Sustrato (kg)',
                                    'sala_nombre' => 'Sala',
                                    'usuario_creador_nombre' => 'Usuario Creador',
                                    'fecha_creacion_lote' => 'Fecha Creación',
                                    'metodologia_inoculacion' => 'Metodología',
                                    'notas_generales_lote' => 'Notas',
                                ],
                                nombreArchivo: 'reporte_lote_produccion_' . $record->lote_id . '.pdf'
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
                    ->visible(fn() => auth()->user()?->tienePermiso('lote_produccion.eliminar') ?? false),

                // Acción bulk para generar reportes
                Tables\Actions\BulkAction::make('generar_reporte_bulk')
                    ->label('Generar Reportes')
                    ->icon('heroicon-o-printer')
                    ->action(function (Collection $records) {
                        try {
                            $reporteGenerador = new ReporteGenerador();
                            $downloadUrl = $reporteGenerador->generarReporte(
                                registros: $records,
                                titulo: 'Reporte de Lotes en Producción',
                                columnas: [
                                    'lote_id' => 'ID Lote',
                                    'cepa_nombre' => 'Cepa',
                                    'lote_inoculo_id' => 'Lote Inóculo ID',
                                    'proceso_esterilizacion_nombre' => 'Proceso Esterilización',
                                    'peso_sustrato_seco_kg' => 'Peso Sustrato (kg)',
                                    'sala_nombre' => 'Sala',
                                    'usuario_creador_nombre' => 'Usuario Creador',
                                    'fecha_creacion_lote' => 'Fecha Creación',
                                    'metodologia_inoculacion' => 'Metodología',
                                    'notas_generales_lote' => 'Notas',
                                ],
                                nombreArchivo: 'reportes_lotes_produccion_' . date('Y-m-d_H-i-s') . '.pdf'
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
            'index' => Pages\ListLoteProduccions::route('/'),
            'create' => Pages\CreateLoteProduccion::route('/create'),
            'view' => Pages\ViewLoteProduccion::route('/{record}'),
            'edit' => Pages\EditLoteProduccion::route('/{record}/edit'),
        ];
    }
}