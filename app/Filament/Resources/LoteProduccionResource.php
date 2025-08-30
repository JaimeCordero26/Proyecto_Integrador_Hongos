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
use Filament\Forms\Get;

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
            Forms\Components\Section::make('Información Básica del Lote')
                ->schema([
                    Forms\Components\Grid::make(2)
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
                                ->required()
                                ->default(now()),
                        ]),
                ]),

            Forms\Components\Section::make('Composición de Sustratos')
                ->description('Especifica qué sustratos usarás y en qué cantidades para este lote')
                ->schema([
                    Forms\Components\Repeater::make('loteSustratos')
                        ->relationship('loteSustratos')
                        ->schema([
                            Forms\Components\Select::make('sustrato_id')
                                ->label('Sustrato')
                                ->relationship('sustrato', 'nombre_sustrato')
                                ->required()
                                ->searchable()
                                ->distinct()
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                            Forms\Components\TextInput::make('cantidad_gramos')
                                ->label('Cantidad (gramos)')
                                ->numeric()
                                ->required()
                                ->suffix('g')
                                ->step(0.1)
                                ->minValue(0.1)
                                ->live(true),
                        ])
                        ->columns(2)
                        ->label('Sustratos utilizados')
                        ->addActionLabel('Agregar sustrato')
                        ->required()
                        ->minItems(1)
                        ->maxItems(10)
                        ->itemLabel(fn (array $state): ?string =>
                            $state['sustrato_id']
                                ? \App\Models\Sustrato::find($state['sustrato_id'])?->nombre_sustrato . ' - ' . ($state['cantidad_gramos'] ?? 0) . 'g'
                                : 'Nuevo sustrato'
                        )
                        ->collapsible()
                        ->cloneable()
                        ->live(true),
                    Forms\Components\Placeholder::make('total_calculado')
                        ->content(function (Get $get) {
                            $items = $get('loteSustratos') ?? [];
                            $totalG = 0.0;
                            foreach ($items as $it) {
                                $totalG += (float) ($it['cantidad_gramos'] ?? 0);
                            }
                            $gIsInt = abs($totalG - round($totalG)) < 0.0005;
                            $gStr = $gIsInt
                                ? number_format((float) round($totalG), 0, '.', '')
                                : number_format($totalG, 1, '.', '');

                            $kg = $totalG / 1000;
                            $kgIsInt = abs($kg - round($kg)) < 0.0005;
                            $kgStr = $kgIsInt
                                ? number_format((float) round($kg), 0, '.', '')
                                : number_format($kg, 3, '.', '');

                            return "{$gStr} g ({$kgStr} Kg)";
                        })

                        ->columnSpanFull()
                        ->helperText('Se calcula automáticamente con base en las cantidades de los sustratos.'),
                ]),

            Forms\Components\Section::make('Metodología y Observaciones')
                ->schema([
                    Forms\Components\Textarea::make('metodologia_inoculacion')
                        ->label('Metodología de Inoculación')
                        ->placeholder('Describe el proceso de inoculación utilizado...')
                        ->rows(3)
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('notas_generales_lote')
                        ->label('Notas Generales del Lote')
                        ->placeholder('Observaciones adicionales, condiciones especiales, etc...')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
        ]);
}


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('lote_id')
                    ->label('ID')
                    ->sortable()
                    ->numeric()
                    ->searchable(),
                Tables\Columns\TextColumn::make('cepa.nombre_cientifico')
                    ->label('Cepa')
                    ->sortable()
                    ->searchable()
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 30 ? $state : null;
                    }),
                Tables\Columns\TextColumn::make('loteInoculo.lote_inoculo_id')
                    ->label('Inóculo ID')
                    ->sortable()
                    ->numeric()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('procesoEsterilizacion.metodo')
                    ->label('Esterilización')
                    ->sortable()
                    ->searchable()
                    ->placeholder('Sin proceso')
                    ->limit(20),
             Tables\Columns\TagsColumn::make('sustratos')
                    ->label('Sustratos')
                    ->getStateUsing(fn(\App\Models\LoteProduccion $record) =>
                        $record->loteSustratos->map(function ($ls) {
                            $nombre = $ls->sustrato?->nombre_sustrato ?? '—';
                            $g = rtrim(rtrim(number_format((float) $ls->cantidad_gramos, 1), '0'), '.');
                            return "{$nombre} ({$g}g)";
                        })->all()
                    )
                    ->limit(4)
                    ->separator(', ')
                    ->searchable(),

                Tables\Columns\TextColumn::make('salaCultivo.nombre_sala')
                    ->label('Sala')
                    ->sortable()
                    ->searchable()
                    ->placeholder('Sin sala')
                    ->limit(15),
                Tables\Columns\TextColumn::make('usuarioCreador.nombre_completo')
                    ->label('Creador')
                    ->sortable()
                    ->searchable()
                    ->limit(20)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 20 ? $state : null;
                    }),
                Tables\Columns\TextColumn::make('fecha_creacion_lote')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable()
                    ->alignCenter(),
            ])
            ->defaultSort('fecha_creacion_lote', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('cepa_id')
                    ->relationship('cepa', 'nombre_cientifico')
                    ->label('Cepa')
                    ->multiple()
                    ->preload(),
                Tables\Filters\SelectFilter::make('proceso_esterilizacion_id')
                    ->relationship('procesoEsterilizacion', 'metodo')
                    ->label('Proceso Esterilización')
                    ->multiple(),
                Tables\Filters\SelectFilter::make('sala_id')
                    ->relationship('salaCultivo', 'nombre_sala')
                    ->label('Sala de Cultivo')
                    ->multiple(),
                Tables\Filters\Filter::make('fecha_creacion')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Hasta'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn ($query, $date) => $query->whereDate('fecha_creacion_lote', '>=', $date)
                            )
                            ->when(
                                $data['created_until'],
                                fn ($query, $date) => $query->whereDate('fecha_creacion_lote', '<=', $date)
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Desde ' . \Carbon\Carbon::parse($data['created_from'])->format('d/m/Y');
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Hasta ' . \Carbon\Carbon::parse($data['created_until'])->format('d/m/Y');
                        }
                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->visible(fn() => auth()->user()?->tienePermiso('lote_produccion.ver') ?? false),
                Tables\Actions\EditAction::make()
                    ->visible(fn() => auth()->user()?->tienePermiso('lote_produccion.editar') ?? false),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn() => auth()->user()?->tienePermiso('lote_produccion.eliminar') ?? false),

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
                                nombreArchivo: 'reporte_lote_produccion_' . $record->lote_id . '.pdf', orientacion: 'landscape'
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

                Tables\Actions\BulkAction::make('generar_reporte_bulk')
                    ->label('Generar PDFs')
                    ->icon('heroicon-o-printer')
                    ->requiresConfirmation()
                    ->modalHeading('Generar Reporte de Lotes en Producción')
                    ->modalDescription(function (Collection $records) {
                        $count = $records->count();
                        $limit = 100;

                        if ($count > $limit) {
                            return "⚠️ ADVERTENCIA: Has seleccionado {$count} registros.
                            Para evitar problemas de memoria, se procesarán solo los primeros {$limit} registros.
                            Te recomendamos usar filtros para reducir la selección.";
                        }

                        return "Se generará un PDF con {$count} lotes seleccionados.";
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
                            $recordsWithRelations = LoteProduccion::whereIn('lote_id', $records->pluck('lote_id'))
                                ->with(['cepa', 'loteInoculo', 'procesoEsterilizacion', 'salaCultivo', 'usuarioCreador', 'loteSustratos.sustrato'])
                                ->get();

                            $reporteGenerador = new ReporteGenerador();
                            $downloadUrl = $reporteGenerador->generarReporte(
                                registros: $recordsWithRelations,
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
                                nombreArchivo: 'reportes_lotes_produccion_' . date('Y-m-d_H-i-s') . '.pdf',
                                orientacion: 'landscape'
                            );

                            \Filament\Notifications\Notification::make()
                                ->title('Reportes generados exitosamente')
                                ->body('Se procesaron ' . $recordsWithRelations->count() . ' lotes.' .
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
                            \Log::error('Error generando reportes de lotes: ' . $e->getMessage());
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
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->with(['loteSustratos.sustrato']);
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
