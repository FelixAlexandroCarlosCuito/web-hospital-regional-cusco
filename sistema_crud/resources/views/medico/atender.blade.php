<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consulta Médica</title>
    <style>
        body { font-family: sans-serif; background: #f4f7f6; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        h1 { color: #1e3a8a; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; }
        .card { border: 1px solid #e2e8f0; padding: 20px; border-radius: 8px; margin-bottom: 20px; background: #f8fafc;}
        label { font-weight: bold; color: #4a5568; display: block; margin-top: 15px; }
        textarea { width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px; font-family: inherit; resize: vertical;}
        .btn { background: #059669; color: white; text-decoration: none; padding: 12px 20px; border-radius: 4px; border: none; cursor: pointer; font-weight: bold; font-size: 16px;}
        .btn:hover { background: #047857; }
        .btn-volver { background: #cbd5e1; color: #2d3748; text-decoration: none; padding: 12px 20px; border-radius: 4px; font-weight: bold; font-size: 16px; margin-right: 10px;}
        .historial-box { background: #fffbeb; border: 1px solid #fde68a; padding: 15px; border-radius: 8px; margin-bottom: 20px;}
    </style>
</head>
<body>
    <div class="container">
        <h1>Consulta Médica</h1>
        
        <div class="card">
            <h3>Datos del Paciente</h3>
            <p><strong>Nombre:</strong> {{ $cita->paciente_nombre }}</p>
            <p><strong>Documento:</strong> {{ $cita->paciente_documento }}</p>
        </div>

        <h3>Historial Previo del Paciente</h3>
        @forelse($historialPrevio as $hist)
            <div class="historial-box">
                <strong>Fecha: {{ $hist->created_at->format('d/m/Y') }}</strong>
                <p><strong>Diagnóstico:</strong> {{ $hist->diagnostico }}</p>
                <p><strong>Tratamiento:</strong> {{ $hist->tratamiento }}</p>
            </div>
        @empty
            <p style="color: #718096; font-style: italic;">Este es un paciente nuevo. No hay historial previo.</p>
        @endforelse

        <hr style="border: 0; border-top: 1px dashed #cbd5e1; margin: 30px 0;">

        <h3>Nueva Consulta</h3>
        <form action="{{ route('medico.guardar', $cita->id) }}" method="POST">
            @csrf
            <label>Diagnóstico:</label>
            <textarea name="diagnostico" rows="4" required placeholder="Escriba el diagnóstico del paciente..."></textarea>

            <label>Tratamiento / Receta:</label>
            <textarea name="tratamiento" rows="4" required placeholder="Escriba los medicamentos o indicaciones..."></textarea>

            <div style="margin-top: 25px;">
                <a href="{{ route('medico.dashboard', $cita->medico_id) }}" class="btn-volver">Cancelar</a>
                <button type="submit" class="btn">Finalizar Consulta y Guardar</button>
            </div>
        </form>
    </div>
</body>
</html>