<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CosechaResource\Pages;
use App\Models\Cosecha;
use App\Models\UnidadProduccion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Filament\Support\HasCrudPermissions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Services\ReporteGenerador;

class CosechaResource extends Resource
{
    use HasCrudPermissions;

    protected static string $permPrefix = 'cosecha';
    protected static ?string $navigationGroup = 'Cultivo';
    protected static ?string $model = Cosecha::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('unidad_id')
                    ->relationship('unidadProduccion', 'codigo_unidad')
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Set $set) {
                        $set('eficiencia_biologica_calculada', null);
                        $set('peso_cosecha_gramos', null);
                    }),

                Forms\Components\TextInput::make('numero_cosecha')
                    ->numeric()
                    ->required(),

                Forms\Components\DatePicker::make('fecha_cosecha')
                    ->required(),

                Forms\Components\TextInput::make('peso_cosecha_gramos')
                    ->label('Peso de la cosecha (g)')
                    ->numeric()
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        $unidadId = $get('unidad_id');
                        $pesoCosechaGramos = $get('peso_cosecha_gramos');

                        if ($unidadId && $pesoCosechaGramos) {
                            $eficiencia = self::calcularEficienciaBiologicaConBD($unidadId, $pesoCosechaGramos);
                            $set('eficiencia_biologica_calculada', $eficiencia);
                        } else {
                            $set('eficiencia_biologica_calculada', null);
                        }
                    }),

                Forms\Components\TextInput::make('eficiencia_biologica_calculada')
                    ->label('Eficiencia Biológica (%)')
                    ->numeric()
                    ->readOnly()
                    ->dehydrated(false)
                    ->suffix('%')
                    ->placeholder('Se calculará automáticamente')
                    ->helperText('Este valor se calcula usando la función de base de datos'),
            ]);
    }

    protected static function calcularEficienciaBiologicaConBD($unidadId, $pesoCosechaGramos): ?float
    {
        try {
            $unidadProduccion = UnidadProduccion::with('loteProduccion')->find($unidadId);
            if (!$unidadProduccion || !$unidadProduccion->loteProduccion) {
                return null;
            }

            $pesoSustratoSecoKg = $unidadProduccion->loteProduccion->peso_sustrato_seco_kg ?? 0;
            if ($pesoSustratoSecoKg <= 0 || $pesoCosechaGramos <= 0) {
                return null;
            }

            $pesoHongosKg = $pesoCosechaGramos / 1000;

            return Cosecha::calcularEficienciaEstatica($pesoHongosKg, $pesoSustratoSecoKg);
        } catch (\Exception $e) {
            \Log::error('Error calculando eficiencia biológica: ' . $e->getMessage());
            return null;
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('unidadProduccion.codigo_unidad')
                    ->label('Unidad de Producción')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('numero_cosecha')
                    ->label('N° Cosecha')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('peso_cosecha_gramos')
                    ->label('Peso (g)')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => number_format($state, 0) . ' g')
                    ->color('success'),

                Tables\Columns\TextColumn::make('eficiencia_biologica_calculada')
                    ->label('Eficiencia (%)')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 2) . '%' : 'N/A')
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state >= 80 => 'success',
                        $state >= 50 => 'warning',
                        $state > 0 => 'danger',
                        default => 'gray'
                    }),

                Tables\Columns\TextColumn::make('fecha_cosecha')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('fecha_cosecha', 'desc')
            ->defaultPaginationPageOption(25)
            ->paginationPageOptions([10, 25, 50, 100])
            ->filters([
                Tables\Filters\SelectFilter::make('unidad_id')
                    ->relationship('unidadProduccion', 'codigo_unidad')
                    ->label('Unidad de Producción')
                    ->searchable(),

                Tables\Filters\Filter::make('eficiencia_alta')
                    ->label('Alta Eficiencia (≥50%)')
                    ->query(fn (Builder $query): Builder => $query->altaEficiencia(50)),

                Tables\Filters\Filter::make('fecha_cosecha')
                    ->form([
                        Forms\Components\DatePicker::make('desde')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('hasta')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['desde'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_cosecha', '>=', $date),
                            )
                            ->when(
                                $data['hasta'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_cosecha', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->visible(fn() => auth()->user()?->tienePermiso('cosecha.ver') ?? false),

                Tables\Actions\EditAction::make()
                    ->visible(fn() => auth()->user()?->tienePermiso('cosecha.editar') ?? false),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn() => auth()->user()?->tienePermiso('cosecha.eliminar') ?? false),
                Tables\Actions\Action::make('generar_reporte')
                    ->label('Generar PDF')
                    ->icon('heroicon-o-printer')
                    ->action(function (Cosecha $record) {
                        try {
                            if (!$record->relationLoaded('unidadProduccion')) {
                                $record->load('unidadProduccion');
                            }

                            $reporteGenerador = new ReporteGenerador();
                            $downloadUrl = $reporteGenerador->generarReporte(
                                registros: collect([$record]),
                                titulo: 'Reporte de Cosecha',
                                columnas: [
                                    'unidad_codigo' => 'Unidad de Producción',
                                    'numero_cosecha' => 'N° Cosecha',
                                    'peso_formato' => 'Peso',
                                    'eficiencia_formato' => 'Eficiencia Biológica',
                                    'fecha_formato' => 'Fecha de Cosecha',
                                ],
                                nombreArchivo: 'reporte_cosecha_' . $record->cosecha_id . '.pdf'
                            );

                            \Filament\Notifications\Notification::make()
                                ->title('Reporte generado exitosamente')
                                ->body('El PDF de la cosecha se ha creado correctamente.')
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
                            \Log::error('Error generando reporte de cosecha: ' . $e->getMessage());
                            \Filament\Notifications\Notification::make()
                                ->title('Error al generar reporte')
                                ->body('Error: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->color('success')
                    ->visible(fn() => auth()->user()?->tienePermiso('cosecha.ver') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->visible(fn() => auth()->user()?->tienePermiso('cosecha.eliminar') ?? false),

                Tables\Actions\BulkAction::make('generar_reporte_bulk')
                    ->label('Generar PDFs')
                    ->icon('heroicon-o-printer')
                    ->requiresConfirmation()
                    ->modalHeading('Generar Reporte de Cosechas')
                    ->modalDescription(function (Collection $records) {
                        $count = $records->count();
                        $limit = 100;

                        if ($count > $limit) {
                            return "⚠️ ADVERTENCIA: Has seleccionado {$count} registros.
                                    Para evitar problemas de memoria, se procesarán solo los primeros {$limit} registros.
                                    Te recomendamos usar filtros para reducir la selección.";
                        }

                        return "Se generará un PDF con {$count} cosechas seleccionadas.";
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
                            $recordsWithRelations = \App\Models\Cosecha::whereIn('cosecha_id', $records->pluck('cosecha_id'))
                                ->with(['unidadProduccion:unidad_id,codigo_unidad'])
                                ->get();

                            $reporteGenerador = new \App\Services\ReporteGenerador();
                            $downloadUrl = $reporteGenerador->generarReporte(
                                registros: $recordsWithRelations,
                                titulo: 'Reporte de Cosechas',
                                columnas: [
                                    'unidad_codigo' => 'Unidad de Producción',
                                    'numero_cosecha' => 'N° Cosecha',
                                    'peso_formato' => 'Peso',
                                    'eficiencia_formato' => 'Eficiencia Biológica',
                                    'fecha_formato' => 'Fecha de Cosecha',
                                ],
                                nombreArchivo: 'reportes_cosechas_' . date('Y-m-d_H-i-s') . '.pdf',
                                orientacion: 'landscape'
                            );

                            \Filament\Notifications\Notification::make()
                                ->title('Reportes generados exitosamente')
                                ->body('Se procesaron ' . $recordsWithRelations->count() . ' cosechas.' .
                                    ($originalCount > $limit ? " (Limitado de {$originalCount} registros)" : ''))
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
                            \Log::error('Error generando reportes de cosechas: ' . $e->getMessage());

                            \Filament\Notifications\Notification::make()
                                ->title('Error al generar reportes')
                                ->body('Error: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->color('success')
                    ->visible(fn() => auth()->user()?->tienePermiso('cosecha.ver') ?? false),
        ])
            ->persistSortInSession()
            ->persistSearchInSession()
            ->persistFiltersInSession();
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCosechas::route('/'),
            'create' => Pages\CreateCosecha::route('/create'),
            'edit' => Pages\EditCosecha::route('/{record}/edit'),
        ];
    }
}
