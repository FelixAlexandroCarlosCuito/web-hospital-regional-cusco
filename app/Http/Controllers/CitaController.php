<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cita;

class CitaController extends Controller
{
    // 1. Guardar la cita desde el formulario HTML
    public function store(Request $request)
    {
        $request->validate([
            'paciente_nombre' => 'required',
            'paciente_documento' => 'required',
            'especialidad' => 'required',
            'fecha' => 'required|date',
            'hora' => 'required',
            'monto_pagar' => 'required|numeric'
        ]);

        $cita = Cita::create([
            'paciente_nombre' => $request->paciente_nombre,
            'paciente_documento' => $request->paciente_documento,
            'especialidad' => $request->especialidad,
            'fecha' => $request->fecha,
            'hora' => $request->hora,
            'monto_pagar' => $request->monto_pagar,
            'estado' => 'pendiente_pago'
        ]);

        return response()->json([
            'mensaje' => 'Cita creada exitosamente',
            'cita' => $cita
        ], 201);
    }

    // 2. Leer la cita para mostrarla en el ticket
    public function show($id)
    {
        $cita = Cita::find($id);

        if (!$cita) {
            return response()->json(['error' => 'Cita no encontrada'], 404);
        }

        return response()->json($cita, 200);
    }
        public function buscarPaciente($documento)
    {
        // Buscamos si ese documento ya tiene alguna cita registrada
        $cita = Cita::where('paciente_documento', $documento)->first();

        if ($cita) {
            return response()->json([
                'encontrado' => true,
                'nombre_completo' => $cita->paciente_nombre
            ]);
        }

        return response()->json(['encontrado' => false]);
    }
}