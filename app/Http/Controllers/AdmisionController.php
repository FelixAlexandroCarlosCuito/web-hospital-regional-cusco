<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Medico;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Gestiona la consulta de agendas por médico y cancelaciones administrativas en ventanilla.
 */
class AdmisionController extends Controller
{
    public function panel(Request $request)
    {
        $medicos = Medico::select(['id', 'especialidad_id', 'nombres', 'apellidos', 'consultorio', 'activo'])
            ->with('especialidad:id,nombre')
            ->orderByDesc('activo')
            ->orderBy('apellidos')
            ->get();

        $medicoId = $request->integer('medico_id');
        $medicoFiltro = $medicoId > 0 ? $medicoId : null;
        $fechaFiltro = $this->resolverFechaValida($request->query('fecha'));

        if ($medicoFiltro) {
            $citas = Cita::select(['id', 'user_id', 'medico_id', 'fecha', 'hora', 'estado', 'codigo_referencia_sis'])
                ->with([
                    'paciente:id,tipo_documento,numero_documento,nombres,apellido_paterno,apellido_materno,telefono,fecha_nacimiento',
                    'medico:id,especialidad_id,nombres,apellidos,consultorio',
                    'medico.especialidad:id,nombre',
                ])
                ->where('medico_id', $medicoFiltro)
                ->enFecha($fechaFiltro)
                ->orderBy('hora', 'asc')
                ->get();
        } else {
            $citas = collect();
        }

        return view('admision.panel', compact('medicos', 'citas', 'medicoFiltro', 'fechaFiltro'));
    }

    public function cancelarCita($id)
    {
        $cita = Cita::select(['id', 'estado'])->findOrFail($id);

        if ($cita->estado !== 'Pendiente') {
            return back()->with('error', 'Solo es posible cancelar citas que se encuentren en estado Pendiente.');
        }

        $cita->update(['estado' => 'Cancelado']);

        return back()->with('success', 'La cita fue cancelada y el cupo quedó liberado.');
    }

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