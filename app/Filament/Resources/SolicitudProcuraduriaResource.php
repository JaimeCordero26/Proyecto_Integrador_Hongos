<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\SolicitudProcuraduria;
use Illuminate\Database\Eloquent\Collection;
use App\Filament\Support\HasCrudPermissions;
use App\Services\ReporteGenerador;
use App\Filament\Resources\SolicitudProcuraduriaResource\Pages;

class SolicitudProcuraduriaResource extends Resource
{
    use HasCrudPermissions;

    protected static string $permPrefix = 'solicitud_procuraduria';
    protected static ?string $model = SolicitudProcuraduria::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Administración';
    protected static ?string $navigationLabel = "Solicitudes Procuraduría";
    protected static ?string $pluralModelLabel = "Solicitudes Procuraduría";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('item_id')
                    ->label('Item de Inventario')
                    ->relationship('item', 'nombre_item')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('descripcion_item_nuevo')
                    ->maxLength(255),
                Forms\Components\Select::make('usuario_solicitante_id')
                    ->relationship('usuarioSolicitante', 'nombre_completo')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\DateTimePicker::make('fecha_solicitud')
                    ->required(),
                Forms\Components\TextInput::make('cantidad_solicitada')
                    ->required()
                    ->numeric(),
                Forms\Components\Textarea::make('justificacion')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('estado_solicitud')
                    ->maxLength(255)
                    ->default('Pendiente'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('item.nombre_item')->label('Item')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('descripcion_item_nuevo')->searchable(),
                Tables\Columns\TextColumn::make('usuarioSolicitante.nombre_completo')->label('Solicitante')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('fecha_solicitud')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('cantidad_solicitada')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('estado_solicitud')->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado_solicitud')->options([
                    'Pendiente' => 'Pendiente',
                    'Aprobada' => 'Aprobada',
                    'Rechazada' => 'Rechazada',
                ])->label('Estado'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->visible(fn() => auth()->user()?->tienePermiso('solicitud_procuraduria.ver') ?? false),
                Tables\Actions\EditAction::make()->visible(fn() => auth()->user()?->tienePermiso('solicitud_procuraduria.editar') ?? false),
                Tables\Actions\DeleteAction::make()->visible(fn() => auth()->user()?->tienePermiso('solicitud_procuraduria.eliminar') ?? false),

                // Acción para generar PDF
                Tables\Actions\Action::make('generar_reporte')
                    ->label('Generar Reporte')
                    ->icon('heroicon-o-printer')
                    ->action(function (SolicitudProcuraduria $record) {
                        $reporteGenerador = new ReporteGenerador();
                        $downloadUrl = $reporteGenerador->generarReporte(
                            registros: collect([$record]),
                            titulo: 'Reporte de Solicitud de Procuraduría',
                            columnas: [
                                'item_nombre' => 'Item',
                                'descripcion_item_nuevo' => 'Descripción',
                                'usuario_solicitante_nombre' => 'Solicitante',
                                'fecha_solicitud' => 'Fecha Solicitud',
                                'cantidad_solicitada' => 'Cantidad',
                                'justificacion' => 'Justificación',
                                'estado_solicitud' => 'Estado',
                            ],
                            nombreArchivo: 'reporte_solicitud_' . $record->solicitud_id . '.pdf', orientacion: 'landscape'
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
                    })
                    ->color('success'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()->visible(fn() => auth()->user()?->tienePermiso('solicitud_procuraduria.eliminar') ?? false),

                Tables\Actions\BulkAction::make('generar_reporte_bulk')
                    ->label('Generar Reportes')
                    ->icon('heroicon-o-printer')
                    ->action(function (Collection $records) {
                        $reporteGenerador = new ReporteGenerador();
                        $downloadUrl = $reporteGenerador->generarReporte(
                            registros: $records,
                            titulo: 'Reporte de Solicitudes de Procuraduría',
                            columnas: [
                                'item_nombre' => 'Item',
                                'descripcion_item_nuevo' => 'Descripción',
                                'usuario_solicitante_nombre' => 'Solicitante',
                                'fecha_solicitud' => 'Fecha Solicitud',
                                'cantidad_solicitada' => 'Cantidad',
                                'justificacion' => 'Justificación',
                                'estado_solicitud' => 'Estado',
                            ],
                            nombreArchivo: 'reportes_solicitudes_' . date('Y-m-d_H-i-s') . '.pdf', orientacion: 'landscape'
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
            'index' => Pages\ListSolicitudProcuradurias::route('/'),
            'create' => Pages\CreateSolicitudProcuraduria::route('/create'),
            'view' => Pages\ViewSolicitudProcuraduria::route('/{record}'),
            'edit' => Pages\EditSolicitudProcuraduria::route('/{record}/edit'),
        ];
    }
}
