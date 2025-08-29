<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AccionResource\Pages;
use App\Filament\Resources\AccionResource\RelationManagers;
use App\Models\Accion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Support\HasCrudPermissions;
use Illuminate\Database\Eloquent\Builder;
use App\Services\ReporteGenerador;
use Illuminate\Support\Collection;

class AccionResource extends Resource
{
    use HasCrudPermissions;
    
    protected static string $permPrefix = 'accion';
    protected static ?string $navigationGroup = 'Auditoría';
    protected static ?string $model = Accion::class;
    protected static ?string $navigationLabel = "Acciones";
    protected static ?string $modelLabel = "Acciones";
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('tabla_afectada')
                    ->maxLength(100),
                Forms\Components\TextInput::make('id_registro')
                    ->numeric(),
                Forms\Components\TextInput::make('tipo_accion')
                    ->maxLength(10),
                Forms\Components\TextInput::make('usuario_id')
                    ->numeric(),
                Forms\Components\DateTimePicker::make('fecha_hora'),
                Forms\Components\Textarea::make('descripcion')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tabla_afectada')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('id_registro')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tipo_accion')
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'CREATE' => 'success',
                        'UPDATE' => 'warning', 
                        'DELETE' => 'danger',
                        'LOGIN' => 'info',
                        'LOGOUT' => 'gray',
                        default => 'secondary',
                    }),
                Tables\Columns\TextColumn::make('usuario.nombre_completo')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('fecha_hora')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('descripcion')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    })
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipo_accion')
                    ->options([
                        'CREATE' => 'Crear',
                        'UPDATE' => 'Actualizar', 
                        'DELETE' => 'Eliminar',
                        'LOGIN' => 'Iniciar Sesión',
                        'LOGOUT' => 'Cerrar Sesión',
                    ]),
                Tables\Filters\SelectFilter::make('tabla_afectada')
                    ->options(function () {
                        return Accion::distinct()
                            ->pluck('tabla_afectada', 'tabla_afectada')
                            ->filter()
                            ->toArray();
                    })
                    ->searchable(),
                Tables\Filters\Filter::make('fecha_hora')
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
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_hora', '>=', $date),
                            )
                            ->when(
                                $data['hasta'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_hora', '<=', $date),
                            );
                    }),
            ])->actions([
                Tables\Actions\ViewAction::make()
                    ->visible(fn() => auth()->user()?->tienePermiso('accion.ver') ?? false),
                
                Tables\Actions\EditAction::make()
                    ->visible(fn() => auth()->user()?->tienePermiso('accion.editar') ?? false),
                
                Tables\Actions\DeleteAction::make()
                    ->visible(fn() => auth()->user()?->tienePermiso('accion.eliminar') ?? false),

                Tables\Actions\Action::make('generar_reporte')
                    ->label('Generar PDF')
                    ->icon('heroicon-o-printer')
                    ->action(function (Accion $record) {
                        try {
                            if (!$record->relationLoaded('usuario')) {
                                $record->load('usuario:usuario_id,nombre_completo');
                            }
                            $reporteGenerador = new ReporteGenerador();
                            $downloadUrl = $reporteGenerador->generarReporte(
                                registros: collect([$record]),
                                titulo: 'Reporte de Acción de Auditoría',
                                columnas: [
                                    'tabla_afectada' => 'Tabla Afectada',
                                    'id_registro' => 'ID Registro',
                                    'tipo_accion' => 'Tipo de Acción',
                                    'usuario_nombre' => 'Usuario',
                                    'fecha_hora_formato' => 'Fecha y Hora',
                                    'descripcion' => 'Descripción',
                                ],
                                nombreArchivo: 'reporte_accion_' . $record->auditoria_id . '.pdf', orientacion: 'landscape'
                            );

                            \Filament\Notifications\Notification::make()
                                ->title('Reporte generado exitosamente')
                                ->body('El PDF de la acción se ha creado correctamente.')
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
                            \Log::error('Error generando reporte de acción: ' . $e->getMessage());
                            \Filament\Notifications\Notification::make()
                                ->title('Error al generar reporte')
                                ->body('Error: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->color('success')
                    ->visible(fn() => auth()->user()?->tienePermiso('accion.ver') ?? false),
            ])->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->visible(fn() => auth()->user()?->tienePermiso('accion.eliminar') ?? false),
                Tables\Actions\BulkAction::make('generar_reporte_bulk')
                    ->label('Generar PDFs')
                    ->icon('heroicon-o-printer')
                    ->requiresConfirmation()
                    ->modalHeading('Generar Reporte de Acciones')
                    ->modalDescription(function (Collection $records) {
                        $count = $records->count();
                        $limit = 100;
                        
                        if ($count > $limit) {
                            return "⚠️ ADVERTENCIA: Has seleccionado {$count} registros. Para evitar problemas de memoria, se procesarán solo los primeros {$limit} registros. Te recomendamos usar filtros para reducir la selección.";
                        }
                        
                        return "Se generará un PDF con {$count} acciones seleccionadas.";
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
                            $recordsWithRelations = Accion::whereIn('auditoria_id', $records->pluck('auditoria_id'))
                                ->with(['usuario:usuario_id,nombre_completo'])
                                ->get();
                            
                            $reporteGenerador = new ReporteGenerador();
                            $downloadUrl = $reporteGenerador->generarReporte(
                                registros: $recordsWithRelations,
                                titulo: 'Reporte de Acciones de Auditoría',
                                columnas: [
                                    'tabla_afectada' => 'Tabla Afectada',
                                    'id_registro' => 'ID Registro',
                                    'tipo_accion' => 'Tipo de Acción',
                                    'usuario_nombre' => 'Usuario',
                                    'fecha_hora_formato' => 'Fecha y Hora',
                                    'descripcion' => 'Descripción',
                                ],
                                nombreArchivo: 'reportes_acciones_' . date('Y-m-d_H-i-s') . '.pdf', orientacion: 'landscape'
                            );

                            \Filament\Notifications\Notification::make()
                                ->title('Reportes generados exitosamente')
                                ->body('Se procesaron ' . $recordsWithRelations->count() . ' acciones.' . 
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
                            \Log::error('Error generando reportes de acciones: ' . $e->getMessage());
                            \Filament\Notifications\Notification::make()
                                ->title('Error al generar reportes')
                                ->body('Error: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->color('success')
                    ->visible(fn() => auth()->user()?->tienePermiso('accion.ver') ?? false),
            ])
            ->defaultSort('auditoria_id', 'desc')
            ->defaultPaginationPageOption(25)
            ->paginationPageOptions([10, 25, 50, 100])
            ->deferLoading()
            ->persistSortInSession()
            ->persistSearchInSession()
            ->persistFiltersInSession();
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAccions::route('/'),
            'create' => Pages\CreateAccion::route('/create'),
            'view' => Pages\ViewAccion::route('/{record}'),
            'edit' => Pages\EditAccion::route('/{record}/edit'),
        ];
    }
}