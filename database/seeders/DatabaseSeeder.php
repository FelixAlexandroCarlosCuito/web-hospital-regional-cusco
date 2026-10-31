<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Medico;
use App\Models\Especialidad;
use App\Models\ReniecSimulacion;
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
        ReniecSimulacion::create([
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
        $cardiologia = Especialidad::create(['nombre' => 'Cardiología']);
        $medicinaGeneral = Especialidad::create(['nombre' => 'Medicina General']);

        // 2. RRHH Crea la cuenta de acceso para el Dr. Carlos
        $usuarioDrCarlos = User::create([
            'tipo_documento' => 'DNI',
            'numero_documento' => '44556677',
            'nombres' => 'Carlos',
            'apellido_paterno' => 'Mendoza',
            'apellido_materno' => 'Quispe',
            'fecha_nacimiento' => '1980-05-10',
            'direccion' => 'Av. La Cultura 123, Cusco',
            'telefono' => '999888777',
            'correo' => 'cmendoza@hospitalregional.gob.pe',
            // Por defecto en el Estado, su primera clave es su DNI
            'password' => bcrypt('44556677'), 
            'rol' => 'medico' // <-- CRÍTICO: Le damos poder de médico
        ]);

        // 3. RRHH le asigna su perfil clínico (Consultorio y Especialidad)
        Medico::create([
            'user_id' => $usuarioDrCarlos->id,
            'especialidad_id' => $cardiologia->id,
            'nombres' => 'Carlos',
            'apellidos' => 'Mendoza Quispe',
            'consultorio' => 'Consultorio 12 - Piso 2'
        ]);

        // 4. Repetimos para el Dr. Sumaq
        $usuarioDrSumaq = User::create([
            'tipo_documento' => 'DNI',
            'numero_documento' => '77665544',
            'nombres' => 'Sumaq',
            'apellido_paterno' => 'Qori',
            'apellido_materno' => 'Tupac',
            'fecha_nacimiento' => '1985-08-20',
            'direccion' => 'San Blas, Cusco',
            'telefono' => '988777666',
            'correo' => 'sqori@hospitalregional.gob.pe',
            'password' => bcrypt('77665544'),
            'rol' => 'medico'
        ]);

        Medico::create([
            'user_id' => $usuarioDrSumaq->id,
            'especialidad_id' => $medicinaGeneral->id,
            'nombres' => 'Sumaq',
            'apellidos' => 'Qori Tupac',
            'consultorio' => 'Consultorio 05 - Piso 1'
        ]);
    }

}
