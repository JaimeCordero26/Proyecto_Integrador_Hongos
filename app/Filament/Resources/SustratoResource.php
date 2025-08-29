<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Sustrato;
use Filament\Resources\Resource;
use App\Filament\Support\HasCrudPermissions;
use Illuminate\Database\Eloquent\Collection;
use App\Services\ReporteGenerador;
use App\Filament\Resources\SustratoResource\Pages;

class SustratoResource extends Resource
{
    use HasCrudPermissions;

    protected static string $permPrefix = 'sustrato';
    protected static ?string $navigationGroup = 'Cultivo';
    protected static ?string $model = Sustrato::class;
    protected static ?string $pluralModelLabel = "Sustratos";
    protected static ?string $navigationLabel = "Sustratos";
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre_sustrato')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('descripcion')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre_sustrato')
                    ->label('Nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->searchable()
                    ->limit(50), // Opcional: limitar caracteres
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->visible(fn() => auth()->user()?->tienePermiso('sustrato.ver') ?? false),
                Tables\Actions\EditAction::make()
                    ->visible(fn() => auth()->user()?->tienePermiso('sustrato.editar') ?? false),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn() => auth()->user()?->tienePermiso('sustrato.eliminar') ?? false),

                // Acción individual para generar PDF
                Tables\Actions\Action::make('generar_reporte')
                    ->label('Generar Reporte')
                    ->icon('heroicon-o-printer')
                    ->action(function (Sustrato $record) {
                        try {
                            $reporteGenerador = new ReporteGenerador();
                            $downloadUrl = $reporteGenerador->generarReporte(
                                registros: collect([$record]),
                                titulo: 'Reporte de Sustrato',
                                columnas: [
                                    'nombre_sustrato' => 'Nombre',
                                    'descripcion' => 'Descripción',
                                ],
                                nombreArchivo: 'reporte_sustrato_' . $record->id . '.pdf'
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
                    ->visible(fn() => auth()->user()?->tienePermiso('sustrato.eliminar') ?? false),

                // Acción bulk para generar reportes
                Tables\Actions\BulkAction::make('generar_reporte_bulk')
                    ->label('Generar Reportes')
                    ->icon('heroicon-o-printer')
                    ->action(function (Collection $records) {
                        try {
                            $reporteGenerador = new ReporteGenerador();
                            $downloadUrl = $reporteGenerador->generarReporte(
                                registros: $records,
                                titulo: 'Reporte de Sustratos',
                                columnas: [
                                    'nombre_sustrato' => 'Nombre',
                                    'descripcion' => 'Descripción',
                                ],
                                nombreArchivo: 'reportes_sustratos_' . date('Y-m-d_H-i-s') . '.pdf'
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
            'index' => Pages\ListSustratos::route('/'),
            'create' => Pages\CreateSustrato::route('/create'),
            'view' => Pages\ViewSustrato::route('/{record}'),
            'edit' => Pages\EditSustrato::route('/{record}/edit'),
        ];
    }
}
