<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Cepa;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Collection;
use App\Filament\Support\HasCrudPermissions;
use App\Filament\Resources\CepaResource\Pages;
use App\Filament\Resources\CepaResource\RelationManagers;
use App\Services\ReporteGenerador;

class CepaResource extends Resource
{
    use HasCrudPermissions;

    protected static ?string $model = Cepa::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Cultivo';
    protected static string $permPrefix = 'cepa';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre_comun')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nombre_cientifico')
                    ->maxLength(255),
                Forms\Components\TextInput::make('codigo_interno')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre_comun')->searchable(),
                Tables\Columns\TextColumn::make('nombre_cientifico')->searchable(),
                Tables\Columns\TextColumn::make('codigo_interno')->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->visible(fn () => auth()->user()?->tienePermiso('cepa.ver') ?? false),
                Tables\Actions\EditAction::make()->visible(fn () => auth()->user()?->tienePermiso('cepa.editar') ?? false),
                Tables\Actions\DeleteAction::make()->visible(fn () => auth()->user()?->tienePermiso('cepa.eliminar') ?? false),

                // Acción de generar reporte individual
                Tables\Actions\Action::make('generar_reporte')
                    ->label('Generar Reporte')
                    ->icon('heroicon-o-printer')
                    ->action(function (Cepa $record) {
                        try {
                            $reporteGenerador = new ReporteGenerador();
                            $downloadUrl = $reporteGenerador->generarReporte(
                                registros: collect([$record]),
                                titulo: 'Reporte de Cepa',
                                columnas: [
                                    'nombre_comun' => 'Nombre Común',
                                    'nombre_cientifico' => 'Nombre Científico',
                                    'codigo_interno' => 'Código Interno',
                                ],
                                nombreArchivo: 'reporte_cepa_' . $record->id . '.pdf'
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
                Tables\Actions\DeleteBulkAction::make()->visible(fn () => auth()->user()?->tienePermiso('cepa.eliminar') ?? false),

                // Acción de generar reportes en bulk
                Tables\Actions\BulkAction::make('generar_reporte_bulk')
                    ->label('Generar Reportes')
                    ->icon('heroicon-o-printer')
                    ->action(function (Collection $records) {
                        try {
                            $reporteGenerador = new ReporteGenerador();
                            $downloadUrl = $reporteGenerador->generarReporte(
                                registros: $records,
                                titulo: 'Reporte de Cepas',
                                columnas: [
                                    'nombre_comun' => 'Nombre Común',
                                    'nombre_cientifico' => 'Nombre Científico',
                                    'codigo_interno' => 'Código Interno',
                                ],
                                nombreArchivo: 'reportes_cepas_' . date('Y-m-d_H-i-s') . '.pdf'
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCepas::route('/'),
            'create' => Pages\CreateCepa::route('/create'),
            'view' => Pages\ViewCepa::route('/{record}'),
            'edit' => Pages\EditCepa::route('/{record}/edit'),
        ];
    }
}
