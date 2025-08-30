<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoteInoculoResource\Pages;
use App\Models\LoteInoculo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Support\HasCrudPermissions;
use Illuminate\Database\Eloquent\Collection;
use App\Services\ReporteGenerador;

class LoteInoculoResource extends Resource
{
    use HasCrudPermissions;

    protected static string $permPrefix = 'lote_inoculo';
    protected static ?string $navigationGroup = 'Cultivo';
    protected static ?string $model = LoteInoculo::class;
    protected static ?string $navigationIcon = 'heroicon-o-beaker';
    protected static ?string $navigationLabel = "Lotes Inóculo";
    protected static ?string $pluralModelLabel = "Lotes Inóculo";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('cepa_id')
                    ->relationship('cepa', 'nombre_cientifico')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('usuario_creador_id')
                    ->relationship('usuarioCreador', 'nombre_completo')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\DatePicker::make('fecha_creacion')
                    ->required(),
                Forms\Components\TextInput::make('sustrato_grano')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('generacion')
                    ->maxLength(255),
                Forms\Components\Select::make('proceso_esterilizacion_id')
                    ->relationship('procesoEsterilizacion', 'metodo')
                    ->searchable()
                    ->preload(),
                Forms\Components\Textarea::make('notas')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('cepa.nombre_cientifico')
                    ->label('Cepa')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('usuarioCreador.nombre_completo')
                    ->label('Usuario Creador')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('fecha_creacion')
                    ->label('Fecha Creación')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sustrato_grano')
                    ->label('Sustrato/Grano')
                    ->searchable(),
                Tables\Columns\TextColumn::make('generacion')
                    ->label('Generación')
                    ->searchable(),
                Tables\Columns\TextColumn::make('procesoEsterilizacion.metodo')
                    ->label('Proceso Esterilización')
                    ->sortable()
                    ->placeholder('Sin proceso'),
            ])
            ->defaultSort('fecha_creacion', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('cepa_id')
                    ->relationship('cepa', 'nombre_cientifico')
                    ->label('Cepa'),
                Tables\Filters\SelectFilter::make('proceso_esterilizacion_id')
                    ->relationship('procesoEsterilizacion', 'metodo')
                    ->label('Proceso Esterilización'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->visible(fn() => auth()->user()?->tienePermiso('lote_inoculo.ver') ?? false),
                Tables\Actions\EditAction::make()
                    ->visible(fn() => auth()->user()?->tienePermiso('lote_inoculo.editar') ?? false),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn() => auth()->user()?->tienePermiso('lote_inoculo.eliminar') ?? false),

                Tables\Actions\Action::make('generar_reporte')
                    ->label('Generar Reporte')
                    ->icon('heroicon-o-printer')
                    ->action(function (LoteInoculo $record) {
                        try {
                            $reporteGenerador = new ReporteGenerador();
                            $downloadUrl = $reporteGenerador->generarReporte(
                                registros: collect([$record]),
                                titulo: 'Reporte de Lote Inóculo',
                                columnas: [
                                    'cepa_nombre' => 'Cepa',
                                    'usuario_creador_nombre' => 'Usuario Creador',
                                    'fecha_creacion' => 'Fecha Creación',
                                    'sustrato_grano' => 'Sustrato/Grano',
                                    'generacion' => 'Generación',
                                    'proceso_esterilizacion_nombre' => 'Proceso Esterilización',
                                    'notas' => 'Notas',
                                ],
                                nombreArchivo: 'reporte_lote_inoculo_' . $record->lote_inoculo_id . '.pdf'
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
                    ->visible(fn() => auth()->user()?->tienePermiso('lote_inoculo.eliminar') ?? false),

                Tables\Actions\BulkAction::make('generar_reporte_bulk')
                    ->label('Generar PDFs')
                    ->icon('heroicon-o-printer')
                    ->requiresConfirmation()
                    ->modalHeading('Generar Reporte de Lotes Inóculo')
                    ->modalDescription(function (Collection $records) {
                        $count = $records->count();
                        $limit = 100;

                        if ($count > $limit) {
                            return "⚠️ Has seleccionado {$count} registros.
                                    Para evitar problemas de memoria, se procesarán solo los primeros {$limit}.
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
                            $reporteGenerador = new ReporteGenerador();
                            $downloadUrl = $reporteGenerador->generarReporte(
                                registros: $records,
                                titulo: 'Reporte de Lotes Inóculo',
                                columnas: [
                                    'cepa_nombre' => 'Cepa',
                                    'usuario_creador_nombre' => 'Usuario Creador',
                                    'fecha_creacion' => 'Fecha Creación',
                                    'sustrato_grano' => 'Sustrato/Grano',
                                    'generacion' => 'Generación',
                                    'proceso_esterilizacion_nombre' => 'Proceso Esterilización',
                                    'notas' => 'Notas',
                                ],
                                nombreArchivo: 'reportes_lotes_inoculo_' . date('Y-m-d_H-i-s') . '.pdf',
                                orientacion: 'landscape'
                            );

                            \Filament\Notifications\Notification::make()
                                ->title('Reportes generados exitosamente')
                                ->body('Se procesaron ' . $records->count() . ' lotes.' .
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
                            \Log::error('Error generando reportes de lotes: ' . $e->getMessage());

                            \Filament\Notifications\Notification::make()
                                ->title('Error al generar reportes')
                                ->body('Error: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->color('success')
                    ->visible(fn() => auth()->user()?->tienePermiso('lote_inoculo.ver') ?? false),
                ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLoteInoculos::route('/'),
            'create' => Pages\CreateLoteInoculo::route('/create'),
            'view' => Pages\ViewLoteInoculo::route('/{record}'),
            'edit' => Pages\EditLoteInoculo::route('/{record}/edit'),
        ];
    }
}
