<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Reporte de Cosechas</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-color: #f9fafb;
      color: #1f2937;
      padding: 2rem;
    }

    h2 {
      font-size: 1.5rem;
      color: #4f46e5;
      margin-bottom: 1rem;
    }

    .widget {
      background-color: #ffffff;
      border-radius: 0.75rem;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      padding: 1.5rem;
      width: 100%;
      max-width: 900px;
      margin: 2rem auto;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 1rem;
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

    tr:hover {
      background-color: #f3f4f6;
    }

    .totales {
      font-weight: bold;
      color: #374151;
      background-color: #f9fafb;
    }

    .empty-row td {
      text-align: center;
      color: #9ca3af;
      font-style: italic;
    }
  </style>
</head>
<body>
  @foreach($registros->groupBy('unidad_id') as $unidad_id => $grupo)
    <div class="widget">
      <h2>Unidad de produccion</h2>
      <table>
        <thead>
          <tr>
            <th>Cosecha ID</th>
            <th>Número de Cosecha</th>
            <th>Fecha</th>
            <th>Peso (g)</th>
            <th>Eficiencia Biológica (%)</th>
          </tr>
        </thead>
        <tbody>
          @forelse($grupo as $registro)
            <tr>
              <td>{{ $registro->cosecha_id }}</td>
              <td>{{ $registro->numero_cosecha }}</td>
              <td>{{ $registro->fecha_cosecha }}</td>
              <td>{{ $registro->peso_cosecha_gramos }}</td>
              <td>{{ number_format($registro->eficiencia_biologica_calculada, 2) }}</td>
            </tr>
          @empty
            <tr class="empty-row">
              <td colspan="5">No hay registros de cosechas para esta unidad.</td>
            </tr>
          @endforelse

          @php
            $totalPeso = $grupo->sum('peso_cosecha_gramos');
            $promedioEficiencia = $grupo->avg('eficiencia_biologica_calculada');
          @endphp
        </tbody>
      </table>
    </div>
  @endforeach
</body>
</html>
