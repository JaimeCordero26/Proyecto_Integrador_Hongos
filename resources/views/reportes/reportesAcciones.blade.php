<head>
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

    .empty-row td {
      text-align: center;
      color: #9ca3af;
      font-style: italic;
    }
  </style>
</head>
<body>
  <div class="widget">
    <h2>Reporte de Acciones Registradas</h2>
    <table>
      <thead>
        <tr>
          <th>Fecha y Hora</th>
          <th>Usuario</th>
          <th>Tabla Afectada</th>
          <th>Tipo de Accion</th>
          <th>Descripcion</th>
        </tr>
      </thead>
      <tbody>
        @forelse($acciones as $accion)
          <tr>
            <td>{{ $accion->fecha_hora ? $accion->fecha_hora->format('d/m/Y H:i:s') : 'N/A' }}</td>
            <td>{{ $accion->usuario ? $accion->usuario->nombre_completo : 'Usuario no encontrado' }}</td>
            <td>{{ $accion->tabla_afectada }}</td>
            <td>{{ $accion->tipo_accion }}</td>
            <td>{{ $accion->descripcion }}</td>
          </tr>
        @empty
          <tr class="empty-row">
            <td colspan="6">No hay acciones registradas.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</body>
</html>