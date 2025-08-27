<head>
  <title>Reporte Ambiental</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-color: #f9fafb;
      color: #1f2937;
      padding: 2rem;
    }

    h2 {
      font-size: 1.75rem;
      color: #4f46e5;
      margin-bottom: 1.5rem;
    }

    .widget {
      background-color: #ffffff;
      border-radius: 0.75rem;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      padding: 1.5rem;
      width: 100%;
      max-width: 900px;
      margin: auto;
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

    tr:hover {
      background-color: #f3f4f6;
    }

    .empty-row td {
      text-align: center;
      color: #9ca3af;
      font-style: italic;
    }
  </style>
</head>
<body>
        @foreach($registros->groupBy('sala_id') as $salaId => $grupo)
        <div class="widget">
            <h2>Reporte Ambiental de la Sala {{ $salaId }}</h2>
            <table>
                <thead>
                    <tr>
                        <th>Fecha y Hora</th>
                        <th>Temperatura (°C)</th>
                        <th>Humedad (%)</th>
                        <th>CO2 (ppm)</th>
                        <th>lumenes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($grupo as $registro)
                    <tr>
                        <td>{{ $registro->fecha_hora }}</td>
                        <td>{{ $registro->temperatura_celsius }}</td>
                        <td>{{ $registro->humedad_relativa }}</td>
                        <td>{{ $registro->co2_ppm }}</td>
                        <td>{{ $registro->luz_lm}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endforeach
    </body>
</html>
