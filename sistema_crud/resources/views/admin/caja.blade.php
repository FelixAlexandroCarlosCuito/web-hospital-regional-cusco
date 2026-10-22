<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Caja - Hospital Regional</title>
    <style>
        body { font-family: sans-serif; background: #f4f7f6; margin: 0; padding: 20px; }
        .container { max-width: 1000px; margin: auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        h1 { color: #1e3a8a; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; }
        .nav { margin-bottom: 20px; }
        .nav a { margin-right: 15px; text-decoration: none; color: #4a5568; font-weight: bold; }
        .nav a:hover { color: #1e3a8a; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { background: #f8fafc; color: #4a5568;}
        .alerta { background: #ecfdf5; color: #047857; padding: 15px; border-radius: 4px; margin-bottom: 15px; font-weight: bold;}
        .btn-pagar { background: #059669; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-weight: bold;}
        .btn-pagar:hover { background: #047857; }
        .monto { font-weight: bold; color: #dc2626; }
        .tiempo { font-size: 12px; color: #718096; }
    </style>
</head>
<body>
    <div class="container">
        <div class="nav">
            <a href="/admin">← Volver al Panel del Director</a>
        </div>
        
        <h1>Caja: Cobros Pendientes</h1>

        @if(session('exito'))
            <div class="alerta">{{ session('exito') }}</div>
        @endif

        <table>
            <thead>
                <tr>
                    <th>Ticket / Fecha Reserva</th>
                    <th>Paciente</th>
                    <th>Especialidad</th>
                    <th>Cita (Fecha / Hora)</th>
                    <th>Monto</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse($citasPendientes as $cita)
                    <tr>
                        <td>
                            <strong>#{{ str_pad($cita->id, 5, '0', STR_PAD_LEFT) }}</strong><br>
                            <span class="tiempo">{{ $cita->created_at->format('d/m/Y H:i') }}</span>
                        </td>
                        <td>
                            {{ $cita->paciente_nombre }}<br>
                            <span class="tiempo">DNI: {{ $cita->paciente_documento }}</span>
                        </td>
                        <td>{{ $cita->especialidad }}</td>
                        <td>{{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }} - {{ $cita->hora }}</td>
                        <td class="monto">S/ {{ number_format($cita->monto_pagar, 2) }}</td>
                        <td>
                            <form action="{{ route('caja.pagar', $cita->id) }}" method="POST" style="margin:0;">
                                @csrf
                                <button type="submit" class="btn-pagar">💵 Confirmar Pago</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center; padding: 30px;">
                            <strong>No hay cobros pendientes en este momento.</strong><br>
                            <span class="tiempo">Las citas aparecerán aquí cuando los pacientes reserven.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>