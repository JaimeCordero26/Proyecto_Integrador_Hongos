<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UnidadProduccionResource\Pages;
use App\Models\UnidadProduccion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Support\HasCrudPermissions;
use App\Services\ReporteGenerador;
use Illuminate\Support\Collection;
use Filament\Forms\Get;
use Closure;
use App\Models\LoteProduccion;
use App\Models\UnidadProduccion as Unidad;

class UnidadProduccionResource extends Resource
{
    use HasCrudPermissions;

    protected static ?string $navigationGroup = 'Cultivo';
    protected static string $permPrefix = 'unidad_produccion';
    protected static ?string $model = UnidadProduccion::class;
    protected static ?string $navigationLabel = "Unidades de Producción";
    protected static ?string $pluralModelLabel = 'Unidades de Producción';
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Select::make('lote_id')
                ->relationship('loteProduccion', 'lote_id')
                ->getOptionLabelFromRecordUsing(fn ($record) => "Lote {$record->lote_id}")
                ->searchable()
                ->required()
                ->placeholder('Selecciona un lote')
                ->preload()
                ->live(),

            Forms\Components\Placeholder::make('disponible_lote')
                ->label('Disponible en el lote')
                ->content(function (Get $get) {
                    $loteId = (int) ($get('lote_id') ?? 0);
                    if (!$loteId) {
                        return '—';
                    }
                    $unidadId = self::unidadActualIdDesdeRuta();
                    $disp = self::gramosDisponibles($loteId, $unidadId);
                    return number_format($disp, 1, '.', '') . ' g';
                })
                ->columnSpanFull(),

            Forms\Components\TextInput::make('codigo_unidad')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('peso_inicial_gramos')
                ->label('Peso inicial (g)')
                ->required()
                ->numeric()
                ->minValue(0.1)

                ->maxValue(function (Get $get) {
                    $loteId = (int) ($get('lote_id') ?? 0);
                    if (!$loteId) return null;
                    $unidadId = self::unidadActualIdDesdeRuta();
                    return self::gramosDisponibles($loteId, $unidadId);
                })
                ->live()

                ->rules([
                    function (Get $get) {
                        return function (string $attribute, $value, Closure $fail) use ($get) {
                            $loteId = (int) ($get('lote_id') ?? 0);
                            if (!$loteId) {
                                return;
                            }
                            $unidadId = self::unidadActualIdDesdeRuta();
                            $disponible = self::gramosDisponibles($loteId, $unidadId);
                            $valor = (float) $value;
                            if ($valor > $disponible + 1e-6) {
                                $fail("No puedes exceder el disponible del lote ({$disponible} g).");
                            }
                            if ($valor <= 0) {
                                $fail('El peso debe ser mayor que 0.');
                            }
                        };
                    },
                ]),

            Forms\Components\DatePicker::make('fecha_inoculacion')
                ->required(),

            Forms\Components\TextInput::make('estado_unidad')
                ->maxLength(255),

            Forms\Components\Select::make('tipo_contaminacion_id')
                ->relationship('tipoContaminacion', 'nombre_comun')
                ->searchable()
                ->preload()
                ->required(),

            Forms\Components\Textarea::make('notas_contaminacion')
                ->columnSpanFull(),
        ]);
}


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('lote_id')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('codigo_unidad')
                    ->searchable(),

                Tables\Columns\TextColumn::make('peso_inicial_gramos')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('fecha_inoculacion')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('estado_unidad')
                    ->searchable(),

                Tables\Columns\TextColumn::make('nombre_tipo_contaminacion')
                    ->label('Tipo de Contaminación')
                    ->sortable()
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->visible(fn() => auth()->user()?->tienePermiso('unidad_produccion.ver') ?? false),

                Tables\Actions\EditAction::make()
                    ->visible(fn() => auth()->user()?->tienePermiso('unidad_produccion.editar') ?? false),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn() => auth()->user()?->tienePermiso('unidad_produccion.eliminar') ?? false),

                Tables\Actions\Action::make('pdf_unidad_produccion')
                    ->label('Generar PDF')
                    ->icon('heroicon-o-printer')
                    ->action(function (UnidadProduccion $record) {
                        try {
                            $reporteGenerador = new ReporteGenerador();
                            $downloadUrl = $reporteGenerador->generarReporte(
                                registros: collect([$record]),
                                titulo: 'Reporte de Unidad de Producción',
                                columnas: [
                                    'codigo_unidad' => 'Código Unidad',
                                    'peso_inicial_gramos' => 'Peso Inicial (g)',
                                    'fecha_inoculacion' => 'Fecha Inoculación',
                                    'estado_unidad' => 'Estado Unidad',
                                    'nombre_tipo_contaminacion' => 'Tipo Contaminación',
                                    'notas_contaminacion' => 'Notas Contaminación',
                                    'lote_id' => 'Lote Asociado',
                                ],
                                nombreArchivo: 'reporte_unidad_' . $record->codigo_unidad . '.pdf'
                            );

                            \Filament\Notifications\Notification::make()
                                ->title('Reporte generado exitosamente')
                                ->body('El PDF de la unidad se ha creado correctamente.')
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
                    ->visible(fn() => auth()->user()?->tienePermiso('unidad_produccion.eliminar') ?? false),

                Tables\Actions\BulkAction::make('pdf_unidades_produccion_bulk')
                    ->label('Generar PDFs')
                    ->icon('heroicon-o-printer')
                    ->requiresConfirmation()
                    ->modalHeading('Generar Reporte de Unidades de Producción')
                    ->modalDescription(function (Collection $records) {
                        $count = $records->count();
                        $limit = 100;

                        if ($count > $limit) {
                            return "⚠️ ADVERTENCIA: Has seleccionado {$count} unidades. Para evitar problemas de memoria, se procesarán solo las primeras {$limit} unidades. Te recomendamos usar filtros para reducir la selección.";
                        }

                        return "Se generará un PDF con {$count} unidades seleccionadas.";
                    })
                    ->modalSubmitActionLabel('Generar PDF')
                    ->action(function (Collection $records) {
                        $limit = 100;
                        $originalCount = $records->count();

                        if ($originalCount > $limit) {
                            $records = $records->take($limit);

                            \Filament\Notifications\Notification::make()
                                ->title('Información')
                                ->body("Se limitó el reporte a {$limit} unidades de {$originalCount} seleccionadas para evitar problemas de memoria.")
                                ->warning()
                                ->duration(5000)
                                ->send();
                        }

                        try {
                            $reporteGenerador = new ReporteGenerador();
                            $downloadUrl = $reporteGenerador->generarReporte(
                                registros: $records,
                                titulo: 'Reporte de Unidades de Producción',
                                columnas: [
                                    'codigo_unidad' => 'Código Unidad',
                                    'peso_inicial_gramos' => 'Peso Inicial (g)',
                                    'fecha_inoculacion' => 'Fecha Inoculación',
                                    'estado_unidad' => 'Estado Unidad',
                                    'nombre_tipo_contaminacion' => 'Tipo Contaminación',
                                    'notas_contaminacion' => 'Notas Contaminación',
                                    'lote_id' => 'Lote Asociado',
                                ],
                                nombreArchivo: 'reportes_unidades_' . date('Y-m-d_H-i-s') . '.pdf'
                            );

                            \Filament\Notifications\Notification::make()
                                ->title('Reportes generados exitosamente')
                                ->body('Se procesaron ' . $records->count() . ' unidades.' .
                                    ($originalCount > $limit ? " (Limitado de {$originalCount} unidades)" : ''))
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
                    ->visible(fn() => auth()->user()?->tienePermiso('unidad_produccion.ver') ?? false),
                ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUnidadProduccions::route('/'),
            'create' => Pages\CreateUnidadProduccion::route('/create'),
            'view' => Pages\ViewUnidadProduccion::route('/{record}'),
            'edit' => Pages\EditUnidadProduccion::route('/{record}/edit'),
        ];
    }
    protected static function gramosTotalesDelLote(int $loteId): float
{
    $lote = LoteProduccion::with('loteSustratos')->find($loteId);
    if (!$lote) {
        return 0.0;
    }
    return (float) ($lote->peso_sustrato_seco_kg * 1000);
}

protected static function gramosAsignadosEnUnidades(int $loteId, ?int $excluirUnidadId = null): float
{
    $q = Unidad::query()->where('lote_id', $loteId);
    if ($excluirUnidadId) {
        $q->where('unidad_id', '!=', $excluirUnidadId);
    }
    return (float) $q->sum('peso_inicial_gramos');
}

protected static function gramosDisponibles(int $loteId, ?int $excluirUnidadId = null): float
{
    $total = self::gramosTotalesDelLote($loteId);
    $asignado = self::gramosAsignadosEnUnidades($loteId, $excluirUnidadId);
    return max(0.0, $total - $asignado);
}

protected static function unidadActualIdDesdeRuta(): ?int
{
    $record = request()->route('record');
    return $record ? (int) $record : null;
}

}
