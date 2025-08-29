<?php
namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class ReporteGenerador
{
    /**
     * Genera un reporte universal para cualquier tipo de registros.
     */
    public function generarReporte(
        $registros, 
        string $titulo, 
        array $columnas, 
        string $nombreArchivo, 
        string $groupBy = null, 
        string $orientacion = 'portrait',
        array $opciones = []
    ): string {
        // Verificar si los registros son una colección, si no, convertirlos
        if (!($registros instanceof SupportCollection) && !($registros instanceof EloquentCollection)) {
            $registros = collect($registros);
        }

        // Convertir modelos Eloquent a arrays/objetos limpios - MEJORADO
        $registros = $registros->map(function ($registro) use ($columnas) {
            if (is_object($registro) && method_exists($registro, 'toArray')) {
                // Obtener array básico
                $data = $registro->toArray();
                
                // NUEVO: Agregar accessors y relaciones manualmente
                foreach ($columnas as $campo => $etiqueta) {
                    if (!isset($data[$campo])) {
                        // Intentar obtener el valor del modelo directamente (accessors, relaciones, etc.)
                        try {
                            if (method_exists($registro, 'getAttribute')) {
                                $valor = $registro->getAttribute($campo);
                            } else {
                                $valor = $registro->$campo ?? null;
                            }
                            
                            if ($valor !== null) {
                                $data[$campo] = $valor;
                            }
                        } catch (\Exception $e) {
                            // Si falla, intentar acceso directo a propiedades/relaciones
                            if (strpos($campo, '.') !== false) {
                                $data[$campo] = $this->obtenerValorAnidado($registro, $campo);
                            } else {
                                $data[$campo] = $registro->$campo ?? null;
                            }
                        }
                    }
                }
            } elseif (is_array($registro)) {
                $data = $registro;
            } else {
                return $registro;
            }
            
            // Limpiar cada campo para asegurar compatibilidad
            $cleanData = [];
            foreach ($data as $key => $value) {
                $cleanData[$key] = $this->limpiarValor($value);
            }
            
            return (object) $cleanData;
        });

        // Generar el HTML del reporte
        $html = $this->generarHTML($registros, $titulo, $columnas, $groupBy, $opciones);

        // Crear directorio si no existe
        $filePath = storage_path('app/public/reportes/' . $nombreArchivo);
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        // Crear PDF y guardarlo
        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('A4', $orientacion);
        $pdf->save($filePath);

        // Retornar URL para descarga
        return asset('storage/reportes/' . $nombreArchivo);
    }

    /**
     * NUEVO: Obtiene valores anidados como 'cepa.nombre_cientifico'
     */
    private function obtenerValorAnidado($registro, string $campo)
    {
        $partes = explode('.', $campo);
        $valor = $registro;
        
        foreach ($partes as $parte) {
            if (is_object($valor) && isset($valor->$parte)) {
                $valor = $valor->$parte;
            } elseif (is_object($valor) && method_exists($valor, 'getAttribute')) {
                $valor = $valor->getAttribute($parte);
            } else {
                return null;
            }
        }
        
        return $valor;
    }

    /**
     * Genera el HTML del reporte con estilos incluidos.
     */
    private function generarHTML($registros, string $titulo, array $columnas, string $groupBy = null, array $opciones = []): string
    {
        $html = $this->getCSS();
        
        $html .= '<body>';
        
        if ($groupBy && $registros->isNotEmpty() && isset($registros->first()->$groupBy)) {
            // Reporte agrupado
            $html .= $this->generarReporteAgrupado($registros, $titulo, $columnas, $groupBy, $opciones);
        } else {
            // Reporte simple (sin agrupación)
            $html .= $this->generarReporteSimple($registros, $titulo, $columnas, $opciones);
        }
        
        $html .= '</body></html>';
        
        return $html;
    }

    /**
     * Obtiene los estilos CSS del reporte.
     */
    private function getCSS(): string
    {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9fafb;
            color: #1f2937;
            padding: 2rem;
        }
        h1 {
            font-size: 2rem;
            color: #4f46e5;
            margin-bottom: 2rem;
            text-align: center;
        }
        h2 {
            font-size: 1.75rem;
            color: #4f46e5;
            margin-bottom: 1.5rem;
        }
        .info {
            background-color: #f0f9ff;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid #0ea5e9;
        }
        .widget {
            background-color: #ffffff;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
            width: 100%;
            margin: auto;
            margin-bottom: 2rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        thead {
            background-color: #eef2ff;
        }
        th, td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        th {
            font-weight: 600;
            color: #374151;
        }
        td {
            color: #6b7280;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .empty-row td {
            text-align: center;
            color: #9ca3af;
            font-style: italic;
        }
        .summary {
            background-color: #f0f9ff;
            border: 1px solid #0ea5e9;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        .summary h4 {
            color: #0ea5e9;
            margin: 0 0 0.5rem 0;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>';
    }

    /**
     * Genera un reporte agrupado.
     */
    private function generarReporteAgrupado($registros, string $titulo, array $columnas, string $groupBy, array $opciones): string
    {
        $html = "<h1>{$titulo}</h1>";
        $html .= '<div class="info">';
        $html .= '<p><strong>Fecha de generacion:</strong> ' . date('d/m/Y H:i:s') . '</p>';
        $html .= '<p><strong>Total de registros:</strong> ' . $registros->count() . '</p>';
        $html .= '</div>';
        
        // Función para obtener el valor de agrupación
        $grupos = $registros->groupBy(function ($registro) use ($groupBy) {
            return $registro->$groupBy ?? 'Sin clasificar';
        });

        foreach ($grupos as $grupoValor => $grupo) {
            $tituloGrupo = $opciones['grupo_titulo'] ?? $groupBy;
            $html .= '<div class="widget">';
            $html .= "<h2>{$titulo} - {$tituloGrupo} {$grupoValor}</h2>";
            
            $html .= $this->generarTabla($grupo, $columnas);
            $html .= '</div>';
            
            // Agregar salto de página entre grupos si está habilitado
            if (isset($opciones['salto_pagina_grupos']) && $opciones['salto_pagina_grupos']) {
                $html .= '<div class="page-break"></div>';
            }
        }
        
        return $html;
    }

    /**
     * Genera un reporte simple sin agrupación.
     */
    private function generarReporteSimple($registros, string $titulo, array $columnas, array $opciones): string
    {
        $html = "<h1>{$titulo}</h1>";
        $html .= '<div class="info">';
        $html .= '<p><strong>Fecha de generacion:</strong> ' . date('d/m/Y H:i:s') . '</p>';
        $html .= '<p><strong>Total de registros:</strong> ' . $registros->count() . '</p>';
        $html .= '</div>';
        
        $html .= '<div class="widget">';
        $html .= $this->generarTabla($registros, $columnas);
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Genera la tabla HTML con los datos.
     */
    private function generarTabla($registros, array $columnas): string
    {
        $html = '<table><thead><tr>';
        
        // Generar encabezados
        foreach ($columnas as $campo => $etiqueta) {
            // Limpiar caracteres problemáticos de las etiquetas
            $etiqueta = $this->limpiarValor($etiqueta);
            $html .= "<th>{$etiqueta}</th>";
        }
        $html .= '</tr></thead><tbody>';
        
        // Generar filas de datos
        if ($registros->isEmpty()) {
            $html .= '<tr class="empty-row"><td colspan="' . count($columnas) . '">No hay datos disponibles</td></tr>';
        } else {
            foreach ($registros as $registro) {
                $html .= '<tr>';
                foreach ($columnas as $campo => $etiqueta) {
                    $valor = $registro->$campo ?? 'N/A';
                    
                    // Formatear fechas si es necesario
                    if (strpos($campo, 'fecha') !== false || strpos($campo, '_at') !== false) {
                        $valor = $this->formatearFecha($valor);
                    }
                    
                    // Limpiar el valor
                    $valor = $this->limpiarValor($valor);
                    $html .= "<td>{$valor}</td>";
                }
                $html .= '</tr>';
            }
        }
        
        $html .= '</tbody></table>';
        
        return $html;
    }

    /**
     * Limpia valores para evitar problemas de codificación.
     */
    private function limpiarValor($valor)
    {
        if (!is_string($valor)) {
            return $valor;
        }
        
        // Reemplazar caracteres problemáticos
        $reemplazos = [
            '°' => ' grados',
            '²' => '2',
            '³' => '3',
            'µ' => 'u',
            'ñ' => 'n',
            'Ñ' => 'N'
        ];
        
        $valor = str_replace(array_keys($reemplazos), array_values($reemplazos), $valor);
        
        // Escapar para HTML
        return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Formatea fechas para mostrar de forma legible.
     */
    private function formatearFecha($fecha): string
    {
        if (empty($fecha) || $fecha === 'N/A') {
            return 'N/A';
        }
        
        try {
            return \Carbon\Carbon::parse($fecha)->format('d/m/Y H:i');
        } catch (\Exception $e) {
            return $this->limpiarValor((string) $fecha);
        }
    }
}