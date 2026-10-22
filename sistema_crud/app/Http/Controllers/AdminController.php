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
}