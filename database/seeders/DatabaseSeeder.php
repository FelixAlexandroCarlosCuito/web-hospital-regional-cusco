<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ciudadano de prueba para validar el registro
        \App\Models\ReniecSimulacion::create([
            'dni' => '76543210',
            'digito_verificador' => '4',
            'fecha_emision' => '2020-05-15',
            'nombres' => 'JUAN CARLOS',
            'apellido_paterno' => 'PEREZ',
            'apellido_materno' => 'GONZALES',
            'fecha_nacimiento' => '1985-10-24',
            'direccion' => 'Av. El Sol 123, Cusco'
        ]);

        // 1. Crear Especialidades
        $cardiologia = \App\Models\Especialidad::create(['nombre' => 'Cardiología']);
        $medicinaGeneral = \App\Models\Especialidad::create(['nombre' => 'Medicina General']);
        $pediatria = \App\Models\Especialidad::create(['nombre' => 'Pediatría']);

        // 2. Asignar Médicos (Contextualizados al hospital)
        \App\Models\Medico::create([
            'especialidad_id' => $cardiologia->id,
            'nombres' => 'Carlos',
            'apellidos' => 'Mendoza',
            'consultorio' => 'Consultorio 12 - Piso 2'
        ]);

        \App\Models\Medico::create([
            'especialidad_id' => $medicinaGeneral->id,
            'nombres' => 'Sumaq',
            'apellidos' => 'Qori Tupac',
            'consultorio' => 'Consultorio 05 - Piso 1'
        ]);

        \App\Models\Medico::create([
            'especialidad_id' => $pediatria->id,
            'nombres' => 'Amaru',
            'apellidos' => 'Yupanqui',
            'consultorio' => 'Consultorio 08 - Piso 1'
        ]);
    }

}
