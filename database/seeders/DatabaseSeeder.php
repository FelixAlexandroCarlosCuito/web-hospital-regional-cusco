<?php

namespace Database\Seeders;

use App\Models\Cita;
use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\ReniecSimulacion;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // =========================================================
        // 1. PADRÓN SIMULADO DE RENIEC (Para pruebas de registro)
        // =========================================================
        $ciudadanosReniec = [
            // Libres para registrar nuevos pacientes
            [
                'dni'                => '76543210',
                'digito_verificador' => '4',
                'fecha_emision'      => '2020-05-15',
                'nombres'            => 'JUAN CARLOS',
                'apellido_paterno'   => 'PEREZ',
                'apellido_materno'   => 'GONZALES',
                'fecha_nacimiento'   => '1985-10-24',
                'direccion'          => 'Av. El Sol 123, Cusco',
            ],
            [
                'dni'                => '71234568',
                'digito_verificador' => '7',
                'fecha_emision'      => '2021-03-10',
                'nombres'            => 'MARIA ELENA',
                'apellido_paterno'   => 'CONDORI',
                'apellido_materno'   => 'CCAHUANA',
                'fecha_nacimiento'   => '1994-06-12',
                'direccion'          => 'Urb. Magisterio Etapa I, Cusco',
            ],
            [
                'dni'                => '74569812',
                'digito_verificador' => '2',
                'fecha_emision'      => '2019-11-22',
                'nombres'            => 'DIEGO ARMANDO',
                'apellido_paterno'   => 'HUAMAN',
                'apellido_materno'   => 'SOTO',
                'fecha_nacimiento'   => '1999-02-08',
                'direccion'          => 'Av. Collasuyo 410, Cusco',
            ],
            // Ya tendrá cuenta creada abajo (útil para probar detección de cuenta duplicada en Fase 1)
            [
                'dni'                => '70112233',
                'digito_verificador' => '9',
                'fecha_emision'      => '2022-01-18',
                'nombres'            => 'LUZ MARINA',
                'apellido_paterno'   => 'QUISPE',
                'apellido_materno'   => 'MAMANI',
                'fecha_nacimiento'   => '1992-09-14',
                'direccion'          => 'Av. Micaela Bastidas 205, Wanchaq, Cusco',
            ],
        ];

        foreach ($ciudadanosReniec as $ciudadano) {
            ReniecSimulacion::create($ciudadano);
        }

        // =========================================================
        // 2. ESPECIALIDADES (5 activas para poder probar el tope de 3 citas)
        // =========================================================
        $cardiologia     = Especialidad::create(['nombre' => 'Cardiología',      'activo' => true]);
        $medicinaGeneral = Especialidad::create(['nombre' => 'Medicina General', 'activo' => true]);
        $pediatria       = Especialidad::create(['nombre' => 'Pediatría',        'activo' => true]);
        $traumatologia   = Especialidad::create(['nombre' => 'Traumatología',    'activo' => true]);
        $ginecologia     = Especialidad::create(['nombre' => 'Ginecología',      'activo' => true]);

        // =========================================================
        // 3. PERSONAL MÉDICO (5 activos + 1 inactivo para probar bloqueo)
        // =========================================================
        $crearMedico = function (array $datosUser, Especialidad $esp, string $consultorio, bool $activo = true): Medico {
            $user = User::create([
                'rol'              => 'medico',
                'activo'           => $activo,
                'tipo_documento'   => 'DNI',
                'numero_documento' => $datosUser['dni'],
                'nombres'          => $datosUser['nombres'],
                'apellido_paterno' => $datosUser['paterno'],
                'apellido_materno' => $datosUser['materno'],
                'fecha_nacimiento' => $datosUser['nacimiento'],
                'direccion'        => $datosUser['direccion'],
                'telefono'         => $datosUser['telefono'],
                'correo'           => $datosUser['correo'],
                'password'         => Hash::make($datosUser['dni']), // Clave inicial = DNI
            ]);

            return Medico::create([
                'user_id'         => $user->id,
                'especialidad_id' => $esp->id,
                'nombres'         => $datosUser['nombres'],
                'apellidos'       => "{$datosUser['paterno']} {$datosUser['materno']}",
                'consultorio'     => $consultorio,
                'activo'          => $activo,
            ]);
        };

        $drCarlos = $crearMedico([
            'dni' => '44556677', 'nombres' => 'Carlos', 'paterno' => 'Mendoza', 'materno' => 'Quispe',
            'nacimiento' => '1980-05-10', 'direccion' => 'Av. La Cultura 123, Cusco',
            'telefono' => '999888777', 'correo' => 'cmendoza@hospitalregional.gob.pe',
        ], $cardiologia, 'Consultorio 12 - Piso 2');

        $drSumaq = $crearMedico([
            'dni' => '77665544', 'nombres' => 'Sumaq', 'paterno' => 'Qori', 'materno' => 'Tupac',
            'nacimiento' => '1985-08-20', 'direccion' => 'San Blas, Cusco',
            'telefono' => '988777666', 'correo' => 'sqori@hospitalregional.gob.pe',
        ], $medicinaGeneral, 'Consultorio 05 - Piso 1');

        $draLucia = $crearMedico([
            'dni' => '41223344', 'nombres' => 'Lucía', 'paterno' => 'Valdez', 'materno' => 'Rojas',
            'nacimiento' => '1983-11-02', 'direccion' => 'Urb. Larapa Grande B-4, San Jerónimo',
            'telefono' => '974112233', 'correo' => 'lvaldez@hospitalregional.gob.pe',
        ], $pediatria, 'Consultorio 08 - Piso 1');

        $drJorge = $crearMedico([
            'dni' => '42334455', 'nombres' => 'Jorge', 'paterno' => 'Paredes', 'materno' => 'Salas',
            'nacimiento' => '1978-03-19', 'direccion' => 'Av. Pardo 540, Cusco',
            'telefono' => '984223344', 'correo' => 'jparedes@hospitalregional.gob.pe',
        ], $traumatologia, 'Consultorio 14 - Piso 2');

        $draRosa = $crearMedico([
            'dni' => '43445566', 'nombres' => 'Rosa', 'paterno' => 'Gutiérrez', 'materno' => 'Vega',
            'nacimiento' => '1982-07-28', 'direccion' => 'Av. Garcilaso 312, Wanchaq',
            'telefono' => '984334455', 'correo' => 'rgutierrez@hospitalregional.gob.pe',
        ], $ginecologia, 'Consultorio 19 - Piso 3');

        // Médico desactivado (prueba de baja lógica y bloqueo de acceso)
        $crearMedico([
            'dni' => '45556677', 'nombres' => 'Héctor', 'paterno' => 'Salazar', 'materno' => 'Puma',
            'nacimiento' => '1975-01-12', 'direccion' => 'Santiago, Cusco',
            'telefono' => '984556677', 'correo' => 'hsalazar@hospitalregional.gob.pe',
        ], $medicinaGeneral, 'Consultorio 06 - Piso 1', false);

        // =========================================================
        // 4. CUENTAS DE ADMISIÓN Y ADMINISTRADOR
        // =========================================================
        User::create([
            'rol'              => 'admision',
            'activo'           => true,
            'tipo_documento'   => 'DNI',
            'numero_documento' => '11223344',
            'nombres'          => 'Ventanilla',
            'apellido_paterno' => 'Central',
            'apellido_materno' => 'Hospital',
            'fecha_nacimiento' => '1990-01-01',
            'direccion'        => 'Sede Principal - Hospital Regional',
            'telefono'         => '084223344',
            'correo'           => 'admision@hospitalregional.gob.pe',
            'password'         => Hash::make('11223344'),
        ]);

        User::create([
            'rol'              => 'admin',
            'activo'           => true,
            'tipo_documento'   => 'DNI',
            'numero_documento' => '76869228',
            'nombres'          => 'Administrador',
            'apellido_paterno' => 'General',
            'apellido_materno' => 'HRC',
            'fecha_nacimiento' => '1990-01-01',
            'direccion'        => 'Av. La Cultura S/N - Hospital Regional',
            'telefono'         => '084231131',
            'correo'           => 'admin@hospitalregional.gob.pe',
            'password'         => Hash::make('admin1'),
        ]);

        // =========================================================
        // 5. PACIENTES PRE-REGISTRADOS (Para pruebas directas y métricas)
        // =========================================================
        $pacLuz = User::create([
            'rol'              => 'paciente',
            'activo'           => true,
            'tipo_documento'   => 'DNI',
            'numero_documento' => '70112233',
            'nombres'          => 'Luz Marina',
            'apellido_paterno' => 'Quispe',
            'apellido_materno' => 'Mamani',
            'fecha_nacimiento' => '1992-09-14',
            'direccion'        => 'Av. Micaela Bastidas 205, Wanchaq, Cusco',
            'telefono'         => '984110022',
            'correo'           => 'luz.quispe@correo.com',
            'password'         => Hash::make('123456'),
        ]);

        $pacMateo = User::create([
            'rol'              => 'paciente',
            'activo'           => true,
            'tipo_documento'   => 'DNI',
            'numero_documento' => '70223344',
            'nombres'          => 'Mateo',
            'apellido_paterno' => 'Chávez',
            'apellido_materno' => 'Ccahuana',
            'fecha_nacimiento' => '1988-04-30',
            'direccion'        => 'Calle Belén 180, Cusco',
            'telefono'         => '984220033',
            'correo'           => 'mateo.chavez@correo.com',
            'password'         => Hash::make('123456'),
        ]);

        $pacSofia = User::create([
            'rol'              => 'paciente',
            'activo'           => true,
            'tipo_documento'   => 'DNI',
            'numero_documento' => '70334455',
            'nombres'          => 'Sofía',
            'apellido_paterno' => 'Ramos',
            'apellido_materno' => 'Huarca',
            'fecha_nacimiento' => '1996-12-05',
            'direccion'        => 'Yanaoca, Canas - Cusco',
            'telefono'         => '984330044',
            'correo'           => 'sofia.ramos@correo.com',
            'password'         => Hash::make('123456'),
        ]);

        // =========================================================
        // 6. CITAS DE PRUEBA (Relativas a hoy para llenar todos los KPIs y gráficos)
        // =========================================================
        $hoy = Carbon::today();
        $inicioMes = $hoy->copy()->startOfMonth();
        $fechaPasadaMes = fn (int $diasAtras): string => $hoy->copy()->subDays($diasAtras)->max($inicioMes)->toDateString();

        // Días hábiles dentro de la ventana de los próximos 7 días (Lunes a Viernes)
        $habil1 = $hoy->copy()->addWeekdays(1)->toDateString();
        $habil2 = $hoy->copy()->addWeekdays(2)->toDateString();
        $habil3 = $hoy->copy()->addWeekdays(3)->toDateString();
        $habil4 = $hoy->copy()->addWeekdays(4)->toDateString();

        // Cita dentro de 3 horas (garantiza probar el candado de cancelación < 12h)
        $enTresHoras = Carbon::now()->addHours(3);

        $citas = [
            // --- Historial del mes actual (Atendidas y Canceladas -> alimentan Cumplimiento del Mes) ---
            ['user_id' => $pacLuz->id,   'medico_id' => $drSumaq->id,  'fecha' => $fechaPasadaMes(5), 'hora' => '08:00:00', 'codigo_referencia_sis' => 'REF-SIS-2026-0101', 'estado' => 'Atendido'],
            ['user_id' => $pacMateo->id, 'medico_id' => $drCarlos->id, 'fecha' => $fechaPasadaMes(4), 'hora' => '09:00:00', 'codigo_referencia_sis' => null,                'estado' => 'Atendido'],
            ['user_id' => $pacSofia->id, 'medico_id' => $draRosa->id,  'fecha' => $fechaPasadaMes(3), 'hora' => '10:30:00', 'codigo_referencia_sis' => 'REF-SIS-2026-0102', 'estado' => 'Atendido'],
            ['user_id' => $pacMateo->id, 'medico_id' => $drJorge->id,  'fecha' => $fechaPasadaMes(2), 'hora' => '11:00:00', 'codigo_referencia_sis' => null,                'estado' => 'Cancelado'],
            ['user_id' => $pacSofia->id, 'medico_id' => $drSumaq->id,  'fecha' => $fechaPasadaMes(1), 'hora' => '08:30:00', 'codigo_referencia_sis' => 'REF-SIS-2026-0103', 'estado' => 'Atendido'],

            // --- Citas para HOY (alimentan tarjeta "Citas para Hoy" de inmediato) ---
            ['user_id' => $pacMateo->id, 'medico_id' => $drSumaq->id,  'fecha' => $hoy->toDateString(), 'hora' => '08:00:00', 'codigo_referencia_sis' => 'REF-SIS-2026-0201', 'estado' => 'Atendido'],
            ['user_id' => $pacSofia->id, 'medico_id' => $draLucia->id, 'fecha' => $hoy->toDateString(), 'hora' => '09:30:00', 'codigo_referencia_sis' => null,                'estado' => 'Cancelado'],
            // Cita de Luz en 3 horas -> botón "Cancelación cerrada"
            ['user_id' => $pacLuz->id,   'medico_id' => $drCarlos->id, 'fecha' => $enTresHoras->toDateString(), 'hora' => $enTresHoras->format('H:i:00'), 'codigo_referencia_sis' => 'REF-SIS-2026-0202', 'estado' => 'Pendiente'],

            // --- Próximos días hábiles (alimentan gráfico de Tendencia 7 días y Demanda) ---
            // Luz solo queda con 2 pendientes activas (Cardiología + Pediatría) para que puedas probar reservar la 3ra y bloquear la 4ta
            ['user_id' => $pacLuz->id,   'medico_id' => $draLucia->id, 'fecha' => $habil2, 'hora' => '09:00:00', 'codigo_referencia_sis' => null,                'estado' => 'Pendiente'],
            ['user_id' => $pacMateo->id, 'medico_id' => $drCarlos->id, 'fecha' => $habil1, 'hora' => '08:30:00', 'codigo_referencia_sis' => 'REF-SIS-2026-0301', 'estado' => 'Pendiente'],
            ['user_id' => $pacMateo->id, 'medico_id' => $drJorge->id,  'fecha' => $habil1, 'hora' => '10:00:00', 'codigo_referencia_sis' => null,                'estado' => 'Pendiente'],
            ['user_id' => $pacSofia->id, 'medico_id' => $drSumaq->id,  'fecha' => $habil1, 'hora' => '11:00:00', 'codigo_referencia_sis' => 'REF-SIS-2026-0302', 'estado' => 'Pendiente'],
            ['user_id' => $pacSofia->id, 'medico_id' => $draRosa->id,  'fecha' => $habil2, 'hora' => '08:00:00', 'codigo_referencia_sis' => 'REF-SIS-2026-0303', 'estado' => 'Pendiente'],
            ['user_id' => $pacMateo->id, 'medico_id' => $drSumaq->id,  'fecha' => $habil3, 'hora' => '09:30:00', 'codigo_referencia_sis' => null,                'estado' => 'Pendiente'],
            ['user_id' => $pacSofia->id, 'medico_id' => $drJorge->id,  'fecha' => $habil4, 'hora' => '10:30:00', 'codigo_referencia_sis' => 'REF-SIS-2026-0304', 'estado' => 'Pendiente'],
        ];

        foreach ($citas as $cita) {
            Cita::create($cita);
        }
    }
}