<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RegistroAmbiental;
use App\Models\Cosecha;
use App\Models\Accion;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportesController extends Controller
{
    /**
     * Generar reporte PDF ambiental
     */
    public function reporteAmbiental(){
        // Aumentar límite de memoria temporalmente
        ini_set('memory_limit', '256M');
        ini_set('max_execution_time', 120);
        
        try {
            $registros = RegistroAmbiental::orderBy('created_at', 'desc')
                                        ->limit(200)
                                        ->get();
            
            $pdf = Pdf::loadView('reportes.reportesAmbientales', compact('registros'));
            $pdf->setPaper('A4', 'portrait');
            
            return $pdf->download('reporte_ambiental_general.pdf');
            
        } catch (\Exception $e) {
            \Log::error('Error generando reporte ambiental: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Error al generar el reporte ambiental',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Generar reporte PDF de cosechas
     */
    public function reporteCosechaPDF(){
        // Aumentar límite de memoria temporalmente
        ini_set('memory_limit', '256M');
        ini_set('max_execution_time', 120);
        
        try {
            $registros = Cosecha::select([
                            'cosecha_id',
                            'usuario_id', 
                            'lote_id',
                            'fecha_cosecha',
                            'cantidad_kg',
                            'observaciones'
                        ])
                        ->with('unidadProduccion')
                        ->orderBy('fecha_cosecha', 'desc')
                        ->limit(200)
                        ->get();
            
            $pdf = Pdf::loadView('reportes.reportesCosechas', compact('registros'));
            $pdf->setPaper('A4', 'landscape');
            
            return $pdf->download('reporte_cosechas.pdf');
            
        } catch (\Exception $e) {
            \Log::error('Error generando reporte de cosechas: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Error al generar el reporte de cosechas',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar reporte PDF de acciones de auditoría
     */
    public function reporteAccionesPDF(){
        // Aumentar límite de memoria temporalmente
        ini_set('memory_limit', '256M');
        ini_set('max_execution_time', 120);
        
        try {
            $acciones = Accion::select([
                            'auditoria_id',
                            'fecha_hora', 
                            'usuario_id',
                            'tabla_afectada', 
                            'tipo_accion', 
                            'descripcion'
                        ])
                        ->with('usuario:usuario_id,nombre_completo')
                        ->orderBy('fecha_hora', 'desc')
                        ->limit(200)
                        ->get();
            
            $pdf = Pdf::loadView('reportes.reportesAcciones', compact('acciones'));
            $pdf->setPaper('A4', 'landscape');
            
            return $pdf->download('reporte_acciones.pdf');
            
        } catch (\Exception $e) {
            \Log::error('Error generando reporte de acciones: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Error al generar el reporte de acciones',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}