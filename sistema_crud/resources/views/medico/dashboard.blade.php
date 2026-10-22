<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agenda Médica</title>
    <style>
        body { font-family: sans-serif; background: #f4f7f6; margin: 0; padding: 20px; }
        .container { max-width: 900px; margin: auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        h1 { color: #1e3a8a; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; }
        .info-medico { background: #e0f2fe; padding: 15px; border-radius: 8px; margin-bottom: 20px; color: #0369a1; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { background: #f8fafc; color: #4a5568;}
        .btn { background: #1e3a8a; color: white; text-decoration: none; padding: 8px 15px; border-radius: 4px; font-size: 14px;}
        .btn:hover { background: #152c6e; }
        .alerta { background: #ecfdf5; color: #047857; padding: 10px; border-radius: 4px; margin-bottom: 15px;}
    </style>
</head>
<body>
    <div class="container">
        <h1>Panel del Médico</h1>
        
        <div class="info-medico">
            <h2>{{ $medico->nombre }}</h2>
            <p>Especialidad: <strong>{{ $medico->especialidad->nombre }}</strong></p>
        </div>

        @if(session('exito'))
            <div class="alerta">{{ session('exito') }}</div>
        @endif

        <h3>Mi Agenda (Citas Pagadas y Próximas)</h3>
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Paciente</th>
                    <th>Documento</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse($citas as $cita)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}</td>
                        <td>{{ $cita->hora }}</td>
                        <td>{{ $cita->paciente_nombre }}</td>
                        <td>{{ $cita->paciente_documento }}</td>
                        <td>
                            <a href="{{ route('medico.atender', $cita->id) }}" class="btn">🩺 Atender</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center;">No tienes pacientes agendados por ahora.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>