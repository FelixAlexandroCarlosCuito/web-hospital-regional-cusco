<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Especialidad;
use App\Models\Medico;

class AdminController extends Controller
{
    // Mostrar el panel de administración
    public function index()
    {
        $especialidades = Especialidad::all();
        $medicos = Medico::with('especialidad')->get();
        
        return view('admin.dashboard', compact('especialidades', 'medicos'));
    }

    // Guardar una nueva especialidad
    public function guardarEspecialidad(Request $request)
    {
        $request->validate(['nombre' => 'required']);
        Especialidad::create(['nombre' => $request->nombre]);
        return back()->with('exito', 'Especialidad creada exitosamente.');
    }

    // Guardar un nuevo médico
    public function guardarMedico(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'especialidad_id' => 'required|exists:especialidades,id',
            'horario_inicio' => 'required',
            'horario_fin' => 'required'
        ]);

        Medico::create([
            'nombre' => $request->nombre,
            'especialidad_id' => $request->especialidad_id,
            'horario_inicio' => $request->horario_inicio,
            'horario_fin' => $request->horario_fin,
        ]);

        return back()->with('exito', 'Médico registrado exitosamente.');
    }
        // 1. Mostrar la pantalla de la caja con las citas pendientes
    public function indexCaja()
    {
        // Trae solo las citas que no han sido pagadas, ordenadas por fecha
        $citasPendientes = \App\Models\Cita::where('estado', 'pendiente_pago')
                                           ->orderBy('created_at', 'asc')
                                           ->get();
                                           
        return view('admin.caja', compact('citasPendientes'));
    }

    // 2. Confirmar el pago de una cita
    public function confirmarPago($id)
    {
        $cita = \App\Models\Cita::findOrFail($id);
        
        // Cambiamos el estado a pagado
        $cita->estado = 'pagado';
        $cita->save();

        return back()->with('exito', 'Pago registrado. La cita del paciente ' . $cita->paciente_nombre . ' está en vigor.');
    }
}