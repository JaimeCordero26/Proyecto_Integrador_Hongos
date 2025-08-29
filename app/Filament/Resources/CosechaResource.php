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
use App\Services\ReporteGenerador; // Importar ReporteGenerador

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
                        $pesoCosecha = $get('peso_cosecha_gramos');
                        if ($unidadId && $pesoCosecha) {
                            $eficiencia = self::calcularEficienciaBiologica($unidadId, $pesoCosecha);
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
                    ->helperText('Este valor se calcula automáticamente al guardar'),
            ]);
    }

    protected static function calcularEficienciaBiologica($unidadId, $pesoCosechaGramos): ?float
    {
        try {
            $unidadProduccion = UnidadProduccion::with('loteProduccion')->find($unidadId);
            if (!$unidadProduccion || !$unidadProduccion->loteProduccion) return null;

            $pesoSustratoSecoKg = $unidadProduccion->loteProduccion->peso_sustrato_seco_kg ?? 0;
            if ($pesoSustratoSecoKg <= 0 || $pesoCosechaGramos <= 0) return 0;

            $pesoHongosKg = $pesoCosechaGramos / 1000;
            $eficiencia = ($pesoHongosKg / $pesoSustratoSecoKg) * 100;

            return round($eficiencia, 2);
        } catch (\Exception $e) {
            logger('Error calculando eficiencia biológica: ' . $e->getMessage());
            return null;
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('unidadProduccion.codigo_unidad')->label('Unidad de Producción')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('numero_cosecha')->label('N° Cosecha')->sortable(),
                Tables\Columns\TextColumn::make('peso_cosecha_gramos')->label('Peso (g)')->sortable()->formatStateUsing(fn ($state) => number_format($state, 0) . ' g'),
                Tables\Columns\TextColumn::make('eficiencia_biologica_calculada')->label('Eficiencia (%)')->sortable()->formatStateUsing(fn ($state) => $state ? number_format($state, 2) . '%' : 'N/A'),
                Tables\Columns\TextColumn::make('fecha_cosecha')->label('Fecha')->date('d/m/Y')->sortable(),
            ])
            ->defaultSort('fecha_cosecha', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('unidad_id')
                    ->relationship('unidadProduccion', 'codigo_unidad')
                    ->label('Unidad de Producción'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->visible(fn() => auth()->user()?->tienePermiso('cosecha.ver') ?? false),
                Tables\Actions\EditAction::make()->visible(fn() => auth()->user()?->tienePermiso('cosecha.editar') ?? false),
                Tables\Actions\DeleteAction::make()->visible(fn() => auth()->user()?->tienePermiso('cosecha.eliminar') ?? false),

                // Acción individual para generar reporte
                Tables\Actions\Action::make('generar_reporte')
                    ->label('Generar Reporte')
                    ->icon('heroicon-o-printer')
                    ->action(function (Cosecha $record) {
                        try {
                            $reporteGenerador = new ReporteGenerador();
                            $downloadUrl = $reporteGenerador->generarReporte(
                                registros: collect([$record]),
                                titulo: 'Reporte de Cosecha',
                                columnas: [
                                    'unidadProduccion.codigo_unidad' => 'Unidad de Producción',
                                    'numero_cosecha' => 'N° Cosecha',
                                    'peso_cosecha_gramos' => 'Peso (g)',
                                    'eficiencia_biologica_calculada' => 'Eficiencia (%)',
                                    'fecha_cosecha' => 'Fecha',
                                ],
                                nombreArchivo: 'reporte_cosecha_' . $record->id . '.pdf'
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
                Tables\Actions\DeleteBulkAction::make()->visible(fn() => auth()->user()?->tienePermiso('cosecha.eliminar') ?? false),

                // Acción bulk para generar reportes
                Tables\Actions\BulkAction::make('generar_reporte_bulk')
                    ->label('Generar Reportes')
                    ->icon('heroicon-o-printer')
                    ->action(function (Collection $records) {
                        try {
                            $reporteGenerador = new ReporteGenerador();
                            $downloadUrl = $reporteGenerador->generarReporte(
                                registros: $records,
                                titulo: 'Reporte de Cosechas',
                                columnas: [
                                    'unidadProduccion.codigo_unidad' => 'Unidad de Producción',
                                    'numero_cosecha' => 'N° Cosecha',
                                    'peso_cosecha_gramos' => 'Peso (g)',
                                    'eficiencia_biologica_calculada' => 'Eficiencia (%)',
                                    'fecha_cosecha' => 'Fecha',
                                ],
                                nombreArchivo: 'reportes_cosechas_' . date('Y-m-d_H-i-s') . '.pdf'
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
            'index' => Pages\ListCosechas::route('/'),
            'create' => Pages\CreateCosecha::route('/create'),
            'edit' => Pages\EditCosecha::route('/{record}/edit'),
        ];
    }
}
