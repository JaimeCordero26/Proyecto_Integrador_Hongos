<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\SalaCultivo;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Collection;
use App\Filament\Support\HasCrudPermissions;
use App\Services\ReporteGenerador;
use App\Filament\Resources\SalaCultivoResource\Pages;

class SalaCultivoResource extends Resource
{
    use HasCrudPermissions;

    protected static string $permPrefix = 'sala_cultivo';
    protected static ?string $model = SalaCultivo::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationGroup = 'Administración';
    protected static ?string $navigationLabel = 'Salas de Cultivo';
    protected static ?string $pluralModelLabel = 'Salas de Cultivo';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre_sala')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('descripcion')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('proposito')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre_sala')
                    ->searchable(),
                Tables\Columns\TextColumn::make('proposito')
                    ->searchable(),
                Tables\Columns\TextColumn::make('descripcion')
                    ->limit(50)
                    ->wrap(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->visible(fn() => auth()->user()?->tienePermiso('sala_cultivo.ver') ?? false),
                Tables\Actions\EditAction::make()
                    ->visible(fn() => auth()->user()?->tienePermiso('sala_cultivo.editar') ?? false),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn() => auth()->user()?->tienePermiso('sala_cultivo.eliminar') ?? false),

                // Acción individual para generar reporte PDF
                Tables\Actions\Action::make('generar_reporte')
                    ->label('Generar Reporte')
                    ->icon('heroicon-o-printer')
                    ->action(function (SalaCultivo $record) {
                        try {
                            $reporteGenerador = new ReporteGenerador();
                            $downloadUrl = $reporteGenerador->generarReporte(
                                registros: collect([$record]),
                                titulo: 'Reporte de Sala de Cultivo',
                                columnas: [
                                    'nombre_sala' => 'Nombre Sala',
                                    'proposito' => 'Propósito',
                                    'descripcion' => 'Descripción',
                                ],
                                nombreArchivo: 'reporte_sala_cultivo_' . $record->id . '.pdf'
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
                    ->visible(fn() => auth()->user()?->tienePermiso('sala_cultivo.eliminar') ?? false),

                // Acción bulk para generar reportes PDF
                Tables\Actions\BulkAction::make('generar_reporte_bulk')
                    ->label('Generar Reportes')
                    ->icon('heroicon-o-printer')
                    ->action(function (Collection $records) {
                        try {
                            $reporteGenerador = new ReporteGenerador();
                            $downloadUrl = $reporteGenerador->generarReporte(
                                registros: $records,
                                titulo: 'Reporte de Salas de Cultivo',
                                columnas: [
                                    'nombre_sala' => 'Nombre Sala',
                                    'proposito' => 'Propósito',
                                    'descripcion' => 'Descripción',
                                ],
                                nombreArchivo: 'reportes_salas_cultivo_' . date('Y-m-d_H-i-s') . '.pdf'
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
            'index' => Pages\ListSalaCultivos::route('/'),
            'create' => Pages\CreateSalaCultivo::route('/create'),
            'view' => Pages\ViewSalaCultivo::route('/{record}'),
            'edit' => Pages\EditSalaCultivo::route('/{record}/edit'),
        ];
    }
}
