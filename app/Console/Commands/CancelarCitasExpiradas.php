<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cita;
use Carbon\Carbon;

class CancelarCitasExpiradas extends Command
{
    // El nombre del comando
    protected $signature = 'citas:cancelar-expiradas';

    // La descripción
    protected $description = 'Cancela las citas no pagadas después de 20 minutos';

    public function handle()
    {
        // Busca citas pendientes creadas hace más de 20 minutos
        $citasExpiradas = Cita::where('estado', 'pendiente_pago')
                               ->where('created_at', '<=', Carbon::now()->subMinutes(20))
                               ->get();

        // Las cancela
        foreach ($citasExpiradas as $cita) {
            $cita->estado = 'cancelado';
            $cita->save();
        }

        $this->info('Se cancelaron ' . $citasExpiradas->count() . ' citas expiradas.');
    }
}