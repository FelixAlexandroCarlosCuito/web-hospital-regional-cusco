<?php

namespace Database\Seeders;

use App\Http\Controllers\PaginasController;
use App\Models\Cita;
use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\Novedad;
use App\Models\ReniecSimulacion;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Carga el conjunto inicial de datos operativos, clínicos e institucionales
 * para validar flujos de registro, reservas concurrentes, paneles por rol y reportes.
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Asigna rol y activo fuera de asignación masiva en coherencia con el bloqueo de User::$fillable
        $crearUsuario = function (array $atributos, string $rol, bool $activo = true): User {
            $usuario = new User($atributos);
            $usuario->rol = $rol;
            $usuario->activo = $activo;
            $usuario->save();

            return $usuario;
        };

        // Padrón simulado de RENIEC: tres ciudadanos disponibles y uno ya registrado para validar detección de duplicados
        $ciudadanosReniec = [
            [
                'dni' => '60493679',
                'digito_verificador' => '4',
                'fecha_emision' => '2020-05-15',
                'nombres' => 'Josue',
                'apellido_paterno' => 'Vargas',
                'apellido_materno' => 'Portilla',
                'fecha_nacimiento' => '2007-05-04',
                'direccion' => 'Saylla',
            ],
            [
                'dni' => '76543210',
                'digito_verificador' => '4',
                'fecha_emision' => '2020-05-15',
                'nombres' => 'JUAN CARLOS',
                'apellido_paterno' => 'PEREZ',
                'apellido_materno' => 'GONZALES',
                'fecha_nacimiento' => '1985-10-24',
                'direccion' => 'Av. El Sol 123, Cusco',
            ],
            [
                'dni' => '71234568',
                'digito_verificador' => '7',
                'fecha_emision' => '2021-03-10',
                'nombres' => 'MARIA ELENA',
                'apellido_paterno' => 'CONDORI',
                'apellido_materno' => 'CCAHUANA',
                'fecha_nacimiento' => '1994-06-12',
                'direccion' => 'Urb. Magisterio Etapa I, Cusco',
            ],
            [
                'dni' => '74569812',
                'digito_verificador' => '2',
                'fecha_emision' => '2019-11-22',
                'nombres' => 'DIEGO ARMANDO',
                'apellido_paterno' => 'HUAMAN',
                'apellido_materno' => 'SOTO',
                'fecha_nacimiento' => '1999-02-08',
                'direccion' => 'Av. Collasuyo 410, Cusco',
            ],
            [
                'dni' => '70112233',
                'digito_verificador' => '9',
                'fecha_emision' => '2022-01-18',
                'nombres' => 'LUZ MARINA',
                'apellido_paterno' => 'QUISPE',
                'apellido_materno' => 'MAMANI',
                'fecha_nacimiento' => '1992-09-14',
                'direccion' => 'Av. Micaela Bastidas 205, Wanchaq, Cusco',
            ],
        ];

        foreach ($ciudadanosReniec as $ciudadano) {
            ReniecSimulacion::create($ciudadano);
        }

        // Catálogo de especialidades: seis con turnos activos, una sin médicos asignados y una inactiva
        $medicinaGeneral = Especialidad::create([
            'nombre' => 'Medicina General',
            'descripcion' => 'Evaluación clínica integral, diagnóstico inicial, seguimiento de enfermedades crónicas y derivación oportuna a subespecialidades.',
            'activo' => true,
        ]);

        $cardiologia = Especialidad::create([
            'nombre' => 'Cardiología',
            'descripcion' => 'Prevención, diagnóstico y control ambulatorio de hipertensión arterial, arritmias e insuficiencia cardíaca.',
            'activo' => true,
        ]);

        $pediatria = Especialidad::create([
            'nombre' => 'Pediatría',
            'descripcion' => 'Atención médica del recién nacido, niño y adolescente, control de crecimiento, desarrollo y prevención de infecciones respiratorias.',
            'activo' => true,
        ]);

        $traumatologia = Especialidad::create([
            'nombre' => 'Traumatología',
            'descripcion' => 'Manejo especializado de lesiones osteoarticulares, fracturas, secuelas traumáticas y trastornos del aparato locomotor.',
            'activo' => true,
        ]);

        $ginecologia = Especialidad::create([
            'nombre' => 'Ginecología y Obstetricia',
            'descripcion' => 'Control prenatal de alto y bajo riesgo, planificación familiar y diagnóstico de patologías ginecológicas en consulta externa.',
            'activo' => true,
        ]);

        $dermatologia = Especialidad::create([
            'nombre' => 'Dermatología',
            'descripcion' => 'Diagnóstico y tratamiento ambulatorio de afecciones cutáneas, alergias dermatológicas y fotoprotección en zonas de altura.',
            'activo' => true,
        ]);

        // Especialidad activa sin médicos habilitados para validar el estado de disponibilidad temporal en cartera y reserva
        Especialidad::create([
            'nombre' => 'Oftalmología',
            'descripcion' => 'Evaluación de agudeza visual, descarte de cataratas, glaucoma y patologías oculares frecuentes.',
            'activo' => true,
        ]);

        // Especialidad desactivada para validar ocultamiento en portal público y gestión administrativa
        Especialidad::create([
            'nombre' => 'Neurología',
            'descripcion' => 'Evaluación de cefaleas crónicas, epilepsia y trastornos neuromusculares en consulta externa.',
            'activo' => false,
        ]);

        $crearMedico = function (array $datos, Especialidad $especialidad, string $consultorio, bool $activo = true) use ($crearUsuario): Medico {
            $user = $crearUsuario([
                'tipo_documento' => 'DNI',
                'numero_documento' => $datos['dni'],
                'nombres' => $datos['nombres'],
                'apellido_paterno' => $datos['paterno'],
                'apellido_materno' => $datos['materno'],
                'fecha_nacimiento' => $datos['nacimiento'],
                'direccion' => $datos['direccion'],
                'telefono' => $datos['telefono'],
                'correo' => $datos['correo'],
                'password' => Hash::make($datos['dni']),
            ], 'medico', $activo);

            return Medico::create([
                'user_id' => $user->id,
                'especialidad_id' => $especialidad->id,
                'nombres' => $datos['nombres'],
                'apellidos' => "{$datos['paterno']} {$datos['materno']}",
                'consultorio' => $consultorio,
                'activo' => $activo,
            ]);
        };

        // Dos médicos activos en Medicina General permiten validar el balanceo automático de carga diaria
        $drSumaq = $crearMedico([
            'dni' => '77665544',
            'nombres' => 'Sumaq',
            'paterno' => 'Qori',
            'materno' => 'Tupac',
            'nacimiento' => '1985-08-20',
            'direccion' => 'San Blas, Cusco',
            'telefono' => '988777666',
            'correo' => 'sqori@hospitalregional.gob.pe',
        ], $medicinaGeneral, 'Consultorio 05 - Piso 1');

        $crearMedico([
            'dni' => '46667788',
            'nombres' => 'Marisol',
            'paterno' => 'Cárdenas',
            'materno' => 'Loayza',
            'nacimiento' => '1986-04-14',
            'direccion' => 'Urb. Santa Mónica D-12, Wanchaq',
            'telefono' => '984667788',
            'correo' => 'mcardenas@hospitalregional.gob.pe',
        ], $medicinaGeneral, 'Consultorio 07 - Piso 1');

        $drCarlos = $crearMedico([
            'dni' => '44556677',
            'nombres' => 'Carlos',
            'paterno' => 'Mendoza',
            'materno' => 'Quispe',
            'nacimiento' => '1980-05-10',
            'direccion' => 'Av. La Cultura 123, Cusco',
            'telefono' => '999888777',
            'correo' => 'cmendoza@hospitalregional.gob.pe',
        ], $cardiologia, 'Consultorio 12 - Piso 2');

        $draLucia = $crearMedico([
            'dni' => '41223344',
            'nombres' => 'Lucía',
            'paterno' => 'Valdez',
            'materno' => 'Rojas',
            'nacimiento' => '1983-11-02',
            'direccion' => 'Urb. Larapa Grande B-4, San Jerónimo',
            'telefono' => '974112233',
            'correo' => 'lvaldez@hospitalregional.gob.pe',
        ], $pediatria, 'Consultorio 08 - Piso 1');

        $drJorge = $crearMedico([
            'dni' => '42334455',
            'nombres' => 'Jorge',
            'paterno' => 'Paredes',
            'materno' => 'Salas',
            'nacimiento' => '1978-03-19',
            'direccion' => 'Av. Pardo 540, Cusco',
            'telefono' => '984223344',
            'correo' => 'jparedes@hospitalregional.gob.pe',
        ], $traumatologia, 'Consultorio 14 - Piso 2');

        $draRosa = $crearMedico([
            'dni' => '43445566',
            'nombres' => 'Rosa',
            'paterno' => 'Gutiérrez',
            'materno' => 'Vega',
            'nacimiento' => '1982-07-28',
            'direccion' => 'Av. Garcilaso 312, Wanchaq',
            'telefono' => '984334455',
            'correo' => 'rgutierrez@hospitalregional.gob.pe',
        ], $ginecologia, 'Consultorio 19 - Piso 3');

        $drAndres = $crearMedico([
            'dni' => '47778899',
            'nombres' => 'Andrés',
            'paterno' => 'Vargas',
            'materno' => 'Linares',
            'nacimiento' => '1981-09-05',
            'direccion' => 'Av. Túpac Amaru 218, Wanchaq',
            'telefono' => '984778899',
            'correo' => 'avargas@hospitalregional.gob.pe',
        ], $dermatologia, 'Consultorio 21 - Piso 3');

        // Cuenta médica desactivada para validar baja lógica y bloqueo de inicio de sesión
        $crearMedico([
            'dni' => '45556677',
            'nombres' => 'Héctor',
            'paterno' => 'Salazar',
            'materno' => 'Puma',
            'nacimiento' => '1975-01-12',
            'direccion' => 'Santiago, Cusco',
            'telefono' => '984556677',
            'correo' => 'hsalazar@hospitalregional.gob.pe',
        ], $medicinaGeneral, 'Consultorio 06 - Piso 1', false);

        $crearUsuario([
            'tipo_documento' => 'DNI',
            'numero_documento' => '11223344',
            'nombres' => 'Ventanilla',
            'apellido_paterno' => 'Central',
            'apellido_materno' => 'Hospital',
            'fecha_nacimiento' => '1990-01-01',
            'direccion' => 'Sede Principal - Hospital Regional',
            'telefono' => '084223344',
            'correo' => 'admision@hospitalregional.gob.pe',
            'password' => Hash::make('11223344'),
        ], 'admision');

        $admin = $crearUsuario([
            'tipo_documento' => 'DNI',
            'numero_documento' => '76869228',
            'nombres' => 'Administrador',
            'apellido_paterno' => 'General',
            'apellido_materno' => 'HRC',
            'fecha_nacimiento' => '1990-01-01',
            'direccion' => 'Av. La Cultura S/N - Hospital Regional',
            'telefono' => '084231131',
            'correo' => 'admin@hospitalregional.gob.pe',
            'password' => Hash::make('admin1'),
        ], 'admin');

        $pacLuz = $crearUsuario([
            'tipo_documento' => 'DNI',
            'numero_documento' => '70112233',
            'nombres' => 'Luz Marina',
            'apellido_paterno' => 'Quispe',
            'apellido_materno' => 'Mamani',
            'fecha_nacimiento' => '1992-09-14',
            'direccion' => 'Av. Micaela Bastidas 205, Wanchaq, Cusco',
            'telefono' => '984110022',
            'correo' => 'luz.quispe@correo.com',
            'password' => Hash::make('123456'),
        ], 'paciente');

        $pacMateo = $crearUsuario([
            'tipo_documento' => 'DNI',
            'numero_documento' => '70223344',
            'nombres' => 'Mateo',
            'apellido_paterno' => 'Chávez',
            'apellido_materno' => 'Ccahuana',
            'fecha_nacimiento' => '1988-04-30',
            'direccion' => 'Calle Belén 180, Cusco',
            'telefono' => '984220033',
            'correo' => 'mateo.chavez@correo.com',
            'password' => Hash::make('123456'),
        ], 'paciente');

        $pacSofia = $crearUsuario([
            'tipo_documento' => 'DNI',
            'numero_documento' => '70334455',
            'nombres' => 'Sofía',
            'apellido_paterno' => 'Ramos',
            'apellido_materno' => 'Huarca',
            'fecha_nacimiento' => '1996-12-05',
            'direccion' => 'Yanaoca, Canas - Cusco',
            'telefono' => '984330044',
            'correo' => 'sofia.ramos@correo.com',
            'password' => Hash::make('123456'),
        ], 'paciente');

        // Citas relativas a la fecha actual para alimentar indicadores diarios, mensuales y tendencia de siete días
        $hoy = Carbon::today();
        $inicioMes = $hoy->copy()->startOfMonth();
        $fechaPasadaMes = fn (int $diasAtras): string => $hoy->copy()->subDays($diasAtras)->max($inicioMes)->toDateString();

        $habil1 = $hoy->copy()->addWeekdays(1)->toDateString();
        $habil2 = $hoy->copy()->addWeekdays(2)->toDateString();
        $habil3 = $hoy->copy()->addWeekdays(3)->toDateString();
        $habil4 = $hoy->copy()->addWeekdays(4)->toDateString();

        // Cita dentro del margen menor a 12 horas para validar el bloqueo de cancelación del paciente
        $enTresHoras = Carbon::now()->addHours(3);

        $citas = [
            ['user_id' => $pacLuz->id, 'medico_id' => $drSumaq->id, 'fecha' => $fechaPasadaMes(5), 'hora' => '08:00:00', 'codigo_referencia_sis' => 'REF-SIS-2026-0101', 'estado' => 'Atendido'],
            ['user_id' => $pacMateo->id, 'medico_id' => $drCarlos->id, 'fecha' => $fechaPasadaMes(3), 'hora' => '09:00:00', 'codigo_referencia_sis' => null, 'estado' => 'Atendido'],
            ['user_id' => $pacSofia->id, 'medico_id' => $draRosa->id, 'fecha' => $fechaPasadaMes(2), 'hora' => '10:30:00', 'codigo_referencia_sis' => 'REF-SIS-2026-0102', 'estado' => 'Atendido'],
            ['user_id' => $pacMateo->id, 'medico_id' => $drJorge->id, 'fecha' => $fechaPasadaMes(1), 'hora' => '11:00:00', 'codigo_referencia_sis' => null, 'estado' => 'Cancelado'],

            ['user_id' => $pacMateo->id, 'medico_id' => $drSumaq->id, 'fecha' => $hoy->toDateString(), 'hora' => '08:00:00', 'codigo_referencia_sis' => 'REF-SIS-2026-0201', 'estado' => 'Atendido'],
            ['user_id' => $pacSofia->id, 'medico_id' => $draLucia->id, 'fecha' => $hoy->toDateString(), 'hora' => '09:30:00', 'codigo_referencia_sis' => null, 'estado' => 'Cancelado'],
            // Luz mantiene dos citas pendientes activas (una bloqueada por plazo y otra cancelable) para validar tope de tres
            ['user_id' => $pacLuz->id, 'medico_id' => $drCarlos->id, 'fecha' => $enTresHoras->toDateString(), 'hora' => $enTresHoras->format('H:i:00'), 'codigo_referencia_sis' => 'REF-SIS-2026-0202', 'estado' => 'Pendiente'],

            ['user_id' => $pacSofia->id, 'medico_id' => $drSumaq->id, 'fecha' => $habil1, 'hora' => '08:30:00', 'codigo_referencia_sis' => 'REF-SIS-2026-0301', 'estado' => 'Pendiente'],
            ['user_id' => $pacMateo->id, 'medico_id' => $drJorge->id, 'fecha' => $habil1, 'hora' => '10:00:00', 'codigo_referencia_sis' => null, 'estado' => 'Pendiente'],
            ['user_id' => $pacLuz->id, 'medico_id' => $draLucia->id, 'fecha' => $habil2, 'hora' => '09:00:00', 'codigo_referencia_sis' => null, 'estado' => 'Pendiente'],
            ['user_id' => $pacSofia->id, 'medico_id' => $draRosa->id, 'fecha' => $habil3, 'hora' => '08:00:00', 'codigo_referencia_sis' => 'REF-SIS-2026-0302', 'estado' => 'Pendiente'],
            ['user_id' => $pacMateo->id, 'medico_id' => $drAndres->id, 'fecha' => $habil4, 'hora' => '10:30:00', 'codigo_referencia_sis' => null, 'estado' => 'Pendiente'],
        ];

        foreach ($citas as $cita) {
            Cita::create($cita);
        }

        // Comunicados institucionales de las cuatro categorías y un borrador interno
        $novedades = [
            [
                'titulo' => 'Horario oficial de atención en Consultorios Externos y Admisión',
                'categoria' => 'Horarios y Atención',
                'resumen' => 'Se recuerda a los pacientes de Cusco y provincias que la atención ambulatoria se desarrolla de lunes a viernes de 8:00 a. m. a 1:00 p. m.',
                'contenido' => "El Hospital Regional del Cusco informa a la población que los consultorios externos atienden de lunes a viernes en el horario de 8:00 a. m. a 1:00 p. m.\n\nLos pacientes con cita reservada mediante el portal web deben presentarse con 15 minutos de anticipación portando su documento de identidad original y su constancia digital o impresa con código QR.\n\nPara consultas particulares, el pago se efectúa únicamente de forma presencial en las ventanillas de Caja antes de ingresar al consultorio.",
                'publicado' => true,
                'fecha_publicacion' => now()->subDays(1),
            ],
            [
                'titulo' => 'Campaña gratuita de despistaje de hipertensión y riesgo cardiovascular',
                'categoria' => 'Campaña de Salud',
                'resumen' => 'El servicio de Cardiología realizará controles preventivos de presión arterial y orientación nutricional para adultos mayores y pacientes referidos.',
                'contenido' => "Con el objetivo de promover la detección temprana de enfermedades cardiovasculares en la región, el Departamento de Medicina y el servicio de Cardiología llevarán a cabo una jornada preventiva dirigida a mayores de 40 años.\n\nDurante la campaña se realizarán mediciones de presión arterial, evaluación de factores de riesgo y consejería nutricional sin costo adicional para afiliados al Seguro Integral de Salud.\n\nLos interesados deben acercarse al patio principal portando su DNI vigente.",
                'publicado' => true,
                'fecha_publicacion' => now()->subDays(3),
            ],
            [
                'titulo' => 'Implementación de constancias digitales con verificación QR en consulta externa',
                'categoria' => 'Institucional',
                'resumen' => 'Las constancias emitidas por el sistema web incorporan código QR firmado digitalmente para agilizar el control en Admisión y Caja.',
                'contenido' => "Como parte del proceso de modernización de la atención al ciudadano, todas las reservas generadas en el portal del Hospital Regional del Cusco incluyen un código QR de verificación en tiempo real.\n\nEste mecanismo permite al personal de Admisión y Caja confirmar la autenticidad y vigencia del turno al instante, evitando colas innecesarias y el uso de constancias anuladas.\n\nEl paciente puede presentar el documento impreso o directamente desde la pantalla de su teléfono móvil.",
                'publicado' => true,
                'fecha_publicacion' => now()->subDays(6),
            ],
            [
                'titulo' => 'Requisitos vigentes para atención de pacientes referidos por el SIS',
                'categoria' => 'Comunicado',
                'resumen' => 'Recordamos a los usuarios provenientes de establecimientos de salud llevar su Hoja de Referencia vigente y copia de DNI.',
                'contenido' => "La Oficina de Seguros y Admisión recuerda a todos los pacientes afiliados al Seguro Integral de Salud (SIS) que acuden desde centros y puestos de salud de Cusco y provincias los requisitos obligatorios para consulta externa:\n\n1. Documento Nacional de Identidad (DNI) original y copia.\n2. Hoja de Referencia SIS emitida por su establecimiento de origen con código legible.\n3. Constancia de cita reservada en el portal institucional.\n\nLa atención cubierta por el SIS no requiere ningún trámite de pago en Caja.",
                'publicado' => true,
                'fecha_publicacion' => now()->subDays(10),
            ],
            [
                'titulo' => 'Borrador: Programación especial por mantenimiento preventivo de consultorios',
                'categoria' => 'Comunicado',
                'resumen' => 'Aviso interno pendiente de aprobación de Dirección sobre reprogramación temporal de ambientes del tercer piso.',
                'contenido' => "Documento en revisión interna correspondiente al cronograma de mantenimiento preventivo de luminarias y señalética en consultorios externos.\n\nSe publicará una vez confirmadas las fechas por la Jefatura de Consulta Externa.",
                'publicado' => false,
                'fecha_publicacion' => null,
            ],
        ];

        foreach ($novedades as $item) {
            Novedad::create([
                'user_id' => $admin->id,
                'titulo' => $item['titulo'],
                'slug' => Novedad::generarSlugUnico($item['titulo']),
                'categoria' => $item['categoria'],
                'resumen' => $item['resumen'],
                'contenido' => $item['contenido'],
                'imagen' => null,
                'publicado' => $item['publicado'],
                'fecha_publicacion' => $item['fecha_publicacion'],
            ]);
        }

        // Invalida la caché pública para reflejar de inmediato el nuevo conjunto de datos
        PaginasController::limpiarCachePublico();
    }
}