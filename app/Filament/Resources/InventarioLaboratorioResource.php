<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\InventarioLaboratorio;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Support\HasCrudPermissions;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\InventarioLaboratorioResource\Pages;
use App\Filament\Resources\InventarioLaboratorioResource\RelationManagers;

class InventarioLaboratorioResource extends Resource
{
        use HasCrudPermissions;

        protected static string $permPrefix = 'inventario_laboratorio';

    protected static ?string $model = InventarioLaboratorio::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationGroup = 'Administración';

    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre_item')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('descripcion')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('cantidad_total')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('cantidad_disponible')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('ubicacion')
                    ->maxLength(255),
                Forms\Components\TextInput::make('estado_item')
                    ->required()
                    ->maxLength(255),
            ]);
    }

public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('item_id')
                ->label('ID de Item')
                ->sortable()
                ->searchable(),
            Tables\Columns\TextColumn::make('nombre_item')
                ->searchable(),
            Tables\Columns\TextColumn::make('cantidad_total')
                ->numeric()
                ->sortable(),
            Tables\Columns\TextColumn::make('cantidad_disponible')
                ->numeric()
                ->sortable(),
            Tables\Columns\TextColumn::make('ubicacion')
                ->searchable(),
            Tables\Columns\TextColumn::make('estado_item')
                ->searchable(),
        ])
        ->filters([
            //
        ])
        ->actions([
            Tables\Actions\ViewAction::make()
                ->visible(fn() => auth()->user()?->tienePermiso('inventario_laboratorio.ver') ?? false),
            Tables\Actions\EditAction::make()
                ->visible(fn() => auth()->user()?->tienePermiso('inventario_laboratorio.editar') ?? false),
            Tables\Actions\DeleteAction::make()
                ->visible(fn() => auth()->user()?->tienePermiso('inventario_laboratorio.eliminar') ?? false),

            // Acción individual para generar reporte PDF
            Tables\Actions\Action::make('generar_reporte')
                ->label('Reporte')
                ->icon('heroicon-o-printer')
                ->action(function (InventarioLaboratorio $record) {
                    try {
                        $reporteGenerador = new \App\Services\ReporteGenerador();
                        $downloadUrl = $reporteGenerador->generarReporte(
                            registros: collect([$record]),
                            titulo: 'Reporte Inventario Laboratorio',
                            columnas: [
                                'item_id' => 'ID de Item',
                                'nombre_item' => 'Nombre',
                                'descripcion' => 'Descripción',
                                'cantidad_total' => 'Cantidad Total',
                                'cantidad_disponible' => 'Cantidad Disponible',
                                'ubicacion' => 'Ubicación',
                                'estado_item' => 'Estado',
                            ],
                            nombreArchivo: 'reporte_inventario_' . $record->item_id . '.pdf'
                        );

                        \Filament\Notifications\Notification::make()
                            ->title('Reporte generado')
                            ->body('Se ha creado correctamente el PDF.')
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
                            ->title('Error')
                            ->body('No se pudo generar el reporte: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
                ->color('success'),
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make()
                ->visible(fn() => auth()->user()?->tienePermiso('inventario_laboratorio.eliminar') ?? false),

            // Bulk action para generar reportes de múltiples registros
            Tables\Actions\BulkAction::make('generar_reporte_bulk')
                ->label('Generar Reportes')
                ->icon('heroicon-o-printer')
                ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                    try {
                        $reporteGenerador = new \App\Services\ReporteGenerador();
                        $downloadUrl = $reporteGenerador->generarReporte(
                            registros: $records,
                            titulo: 'Reporte Inventario Laboratorio',
                            columnas: [
                                'item_id' => 'ID de Item',
                                'nombre_item' => 'Nombre',
                                'descripcion' => 'Descripción',
                                'cantidad_total' => 'Cantidad Total',
                                'cantidad_disponible' => 'Cantidad Disponible',
                                'ubicacion' => 'Ubicación',
                                'estado_item' => 'Estado',
                            ],
                            nombreArchivo: 'reportes_inventario_' . date('Y-m-d_H-i-s') . '.pdf'
                        );

                        \Filament\Notifications\Notification::make()
                            ->title('Reportes generados')
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
                            ->title('Error')
                            ->body('No se pudieron generar los reportes: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
                ->color('success'),
        ]);
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
            'index' => Pages\ListInventarioLaboratorios::route('/'),
            'create' => Pages\CreateInventarioLaboratorio::route('/create'),
            'view' => Pages\ViewInventarioLaboratorio::route('/{record}'),
            'edit' => Pages\EditInventarioLaboratorio::route('/{record}/edit'),
        ];
    }
}
