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
                        // Resetear eficiencia cuando cambie la unidad
                        $set('eficiencia_biologica_calculada', null);
                        // También resetear peso para forzar recálculo
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
                        
                        // Solo mostrar preview si tenemos ambos valores
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
                    ->dehydrated(false) // NO guardar desde el form, que lo haga el model
                    ->suffix('%')
                    ->placeholder('Se calculará automáticamente')
                    ->helperText('Este valor se calcula automáticamente al guardar'),
            ]);
    }

    /**
     * Método estático para calcular eficiencia biológica
     */
    protected static function calcularEficienciaBiologica($unidadId, $pesoCosechaGramos): ?float
    {
        try {
            // Debug: Log los valores que estamos recibiendo
            logger("Debug - Calculando eficiencia para unidad_id: {$unidadId}, peso: {$pesoCosechaGramos}g");
            
            $unidadProduccion = UnidadProduccion::with('loteProduccion')->find($unidadId);
            
            if (!$unidadProduccion) {
                logger("Debug - No se encontró unidad de producción con ID: {$unidadId}");
                return null;
            }
            
            if (!$unidadProduccion->loteProduccion) {
                logger("Debug - La unidad {$unidadId} no tiene lote de producción asociado");
                return null;
            }
            
            $pesoSustratoSecoKg = $unidadProduccion->loteProduccion->peso_sustrato_seco_kg ?? 0;
            
            logger("Debug - Peso sustrato seco: {$pesoSustratoSecoKg}kg");
            
            if ($pesoSustratoSecoKg <= 0) {
                logger("Debug - Peso del sustrato seco es 0 o negativo");
                return 0;
            }
            
            if ($pesoCosechaGramos <= 0) {
                logger("Debug - Peso de cosecha es 0 o negativo");
                return 0;
            }
            
            $pesoHongosKg = $pesoCosechaGramos / 1000;
            $eficiencia = ($pesoHongosKg / $pesoSustratoSecoKg) * 100;
            
            logger("Debug - Cálculo: ({$pesoHongosKg}kg / {$pesoSustratoSecoKg}kg) * 100 = {$eficiencia}%");
            
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
                Tables\Columns\TextColumn::make('unidadProduccion.codigo_unidad')
                    ->label('Unidad de Producción')
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('numero_cosecha')
                    ->label('N° Cosecha')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('peso_cosecha_gramos')
                    ->label('Peso (g)')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => number_format($state, 0) . ' g'),
                    
                Tables\Columns\TextColumn::make('eficiencia_biologica_calculada')
                    ->label('Eficiencia (%)')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 2) . '%' : 'N/A'),
                    
                Tables\Columns\TextColumn::make('fecha_cosecha')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('fecha_cosecha', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('unidad_id')
                    ->relationship('unidadProduccion', 'codigo_unidad')
                    ->label('Unidad de Producción'),
            ])->actions([
                Tables\Actions\ViewAction::make()->visible(fn() => auth()->user()?->tienePermiso('cosecha.ver') ?? false),
                Tables\Actions\EditAction::make()->visible(fn() => auth()->user()?->tienePermiso('cosecha.editar') ?? false),
                Tables\Actions\DeleteAction::make()->visible(fn() => auth()->user()?->tienePermiso('cosecha.eliminar') ?? false),
            ])->bulkActions([
                Tables\Actions\DeleteBulkAction::make()->visible(fn() => auth()->user()?->tienePermiso('cosecha.eliminar') ?? false),
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
            'index' => Pages\ListCosechas::route('/'),
            'create' => Pages\CreateCosecha::route('/create'),
            'edit' => Pages\EditCosecha::route('/{record}/edit'),
        ];
    }
}