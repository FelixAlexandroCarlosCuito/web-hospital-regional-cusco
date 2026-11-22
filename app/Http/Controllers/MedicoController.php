<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Gestiona la agenda clínica diaria del médico autenticado y el registro de atenciones.
 */
class MedicoController extends Controller
{
    public function panel(Request $request)
    {
        $medico = Auth::user()->medico()
            ->select(['id', 'user_id', 'especialidad_id', 'nombres', 'apellidos', 'consultorio', 'activo'])
            ->with('especialidad:id,nombre')
            ->first();

        if (!$medico) {
            abort(403, 'No se encontró un perfil clínico asociado a esta cuenta.');
        }

        $fechaFiltro = $this->resolverFechaValida($request->query('fecha'));

        $citasHoy = Cita::select(['id', 'user_id', 'medico_id', 'fecha', 'hora', 'estado', 'codigo_referencia_sis'])
            ->with('paciente:id,tipo_documento,numero_documento,nombres,apellido_paterno,apellido_materno,fecha_nacimiento')
            ->where('medico_id', $medico->id)
            ->enFecha($fechaFiltro)
            ->orderBy('hora', 'asc')
            ->get();

        return view('medico.panel', compact('citasHoy', 'medico', 'fechaFiltro'));
    }

    public function atender($id)
    {
        $medico = Auth::user()->medico()->select(['id'])->first();

        if (!$medico) {
            abort(403, 'No se encontró un perfil clínico asociado a esta cuenta.');
        }

        $cita = Cita::select(['id', 'medico_id', 'estado'])->findOrFail($id);

        if ((int) $cita->medico_id !== (int) $medico->id) {
            abort(403, 'No tienes permiso para modificar las citas de otro consultorio.');
        }

        // Impide alterar el estado de citas ya atendidas o anuladas mediante peticiones manipuladas
        if ($cita->estado !== 'Pendiente') {
            return back()->with('error', 'Solo se pueden marcar como atendidas las citas en estado Pendiente.');
        }

        $cita->update(['estado' => 'Atendido']);

        return back()->with('success', 'Atención registrada correctamente.');
    }

    /**
     * Valida el formato ISO de la fecha recibida por query string y retorna la fecha actual ante valores inválidos.
     */
    private function resolverFechaValida(mixed $entrada): string
    {
        if (is_string($entrada) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $entrada)) {
            $fecha = Carbon::createFromFormat('Y-m-d', $entrada);
            if ($fecha && $fecha->format('Y-m-d') === $entrada) {
                return $entrada;
            }
        }

        return now()->toDateString();
    }
}