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
                    ->preload(),

                Forms\Components\TextInput::make('codigo_unidad')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('peso_inicial_gramos')
                    ->required()
                    ->numeric(),

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

                // Acción individual para generar PDF de la Unidad de Producción
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

                // Bulk PDF de Unidades de Producción
                Tables\Actions\BulkAction::make('pdf_unidades_produccion_bulk')
                    ->label('Generar PDFs')
                    ->icon('heroicon-o-printer')
                    ->action(function (Collection $records) {
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
                                ->body('Se procesaron ' . $records->count() . ' unidades.')
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
            'index' => Pages\ListUnidadProduccions::route('/'),
            'create' => Pages\CreateUnidadProduccion::route('/create'),
            'view' => Pages\ViewUnidadProduccion::route('/{record}'),
            'edit' => Pages\EditUnidadProduccion::route('/{record}/edit'),
        ];
    }
}
