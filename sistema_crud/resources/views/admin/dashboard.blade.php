<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Hospital</title>
    <style>
        body { font-family: sans-serif; background: #f4f7f6; margin: 0; padding: 20px; }
        .container { max-width: 900px; margin: auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        h1 { color: #1e3a8a; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;}
        .card { border: 1px solid #e2e8f0; padding: 20px; border-radius: 8px; }
        input, select { width: 100%; padding: 8px; margin: 5px 0 15px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;}
        button { background: #1e3a8a; color: white; border: none; padding: 10px; border-radius: 4px; cursor: pointer; width: 100%; font-weight: bold;}
        button:hover { background: #152c6e; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { background: #f8fafc; color: #4a5568;}
        .alerta { background: #ecfdf5; color: #047857; padding: 10px; border-radius: 4px; margin-bottom: 15px;}
    </style>
</head>
<body>
    <div class="container">
        <h1>Panel del Director (Admin)</h1>

        @if(session('exito'))
            <div class="alerta">{{ session('exito') }}</div>
        @endif

        <div class="grid">
            <!-- Formulario de Especialidades -->
            <div class="card">
                <h3>Nueva Especialidad</h3>
                <form action="{{ route('admin.especialidades.store') }}" method="POST">
                    @csrf
                    <label>Nombre de la Especialidad:</label>
                    <input type="text" name="nombre" required placeholder="Ej. Cardiología">
                    <button type="submit">Guardar Especialidad</button>
                </form>
            </div>

            <!-- Formulario de Médicos -->
            <div class="card">
                <h3>Nuevo Médico</h3>
                <form action="{{ route('admin.medicos.store') }}" method="POST">
                    @csrf
                    <label>Nombre del Médico:</label>
                    <input type="text" name="nombre" required placeholder="Ej. Dr. Carlos Mendoza">
                    
                    <label>Especialidad:</label>
                    <select name="especialidad_id" required>
                        <option value="">Seleccione...</option>
                        @foreach($especialidades as $esp)
                            <option value="{{ $esp->id }}">{{ $esp->nombre }}</option>
                        @endforeach
                    </select>

                    <div style="display:flex; gap:10px;">
                        <div style="flex:1;">
                            <label>Hora Inicio:</label>
                            <input type="time" name="horario_inicio" required>
                        </div>
                        <div style="flex:1;">
                            <label>Hora Fin:</label>
                            <input type="time" name="horario_fin" required>
                        </div>
                    </div>
                    <button type="submit" style="margin-top: 10px;">Guardar Médico</button>
                </form>
            </div>
        </div>

        <hr style="margin: 40px 0; border: 0; border-top: 1px solid #e2e8f0;">

        <h2>Médicos Registrados</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Especialidad</th>
                    <th>Horario</th>
                </tr>
            </thead>
            <tbody>
                @forelse($medicos as $medico)
                    <tr>
                        <td>{{ $medico->id }}</td>
                        <td>{{ $medico->nombre }}</td>
                        <td>{{ $medico->especialidad->nombre }}</td>
                        <td>{{ $medico->horario_inicio }} - {{ $medico->horario_fin }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align:center;">No hay médicos registrados aún.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>