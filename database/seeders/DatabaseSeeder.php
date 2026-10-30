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
            'dni' => '76869228',
            'digito_verificador' => '4',
            'fecha_emision' => '2020-05-15',
            'nombres' => 'FELIX ALEXANDRO',
            'apellido_paterno' => 'CARLOS',
            'apellido_materno' => 'CUITO',
        ]);
    }
}
