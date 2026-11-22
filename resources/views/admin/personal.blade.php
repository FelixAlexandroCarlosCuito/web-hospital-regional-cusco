@extends('layouts.main')

@section('titulo', 'Gestión de Médicos y Personal')
@section('clase_main', 'p-0')

@section('contenido')
    @php
        $totalMedicos = $medicos->count();
        $medicosActivosCount = $medicos->where('activo', true)->count();
        $medicosInactivosCount = $totalMedicos - $medicosActivosCount;

        $totalAdmision = $personalAdmision->count();
        $admisionActivosCount = $personalAdmision->where('activo', true)->count();
        $admisionInactivosCount = $totalAdmision - $admisionActivosCount;

        $totalPersonal = $totalMedicos + $totalAdmision;
        $totalActivosGlobal = $medicosActivosCount + $admisionActivosCount;
        $totalPendientesMedicos = $medicos->sum('citas_pendientes_count');
    @endphp

    <section class="cabecera-interna-mate seccion-pizarra py-4">
        <div class="container py-2">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                <div>
                    <span class="sobretitulo-mate d-block mb-1">Administración de personal</span>
                    <h1 class="h3 fw-bold text-hrc-primario mb-1">Médicos y Personal de Admisión</h1>
                    <p class="text-muted small mb-0">
                        Control de cuentas operativas, asignación de consultorios y estado de acceso al sistema.
                    </p>
                </div>

                <div>
                    <button type="button"
                            class="btn btn-hrc-acento rounded-pill px-4"
                            data-bs-toggle="modal"
                            data-bs-target="#modalCrearPersonal">
                        <i class="bi bi-person-plus-fill me-1"></i> Registrar Personal
                    </button>
                </div>
            </div>

            @include('admin._nav')
        </div>
    </section>

    <section class="py-4 py-md-5">
        <div class="container">

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            @endif

            @if (session('warning'))
                <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <strong class="d-block mb-1">Revise los datos ingresados:</strong>
                    <ul class="mb-0 ps-3 small">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            @endif

            {{-- Indicadores consolidados de personal operativo --}}
            <div class="panel-card-elevada p-4 mb-4">
                <div class="row g-3">
                    <div class="col-6 col-lg-3">
                        <div class="kpi-bloque-interno p-3 h-100">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div class="kpi-icono-cuadro kpi-icono-azul">
                                    <i class="bi bi-person-badge"></i>
                                </div>
                                <div class="small fw-bold text-hrc-primario lh-sm">Médicos Activos</div>
                            </div>
                            <div class="d-flex align-items-baseline gap-2">
                                <span class="h3 fw-bold text-hrc-primario mb-0">{{ $medicosActivosCount }}</span>
                                <span class="texto-xs text-muted">/ {{ $totalMedicos }} registrados</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-lg-3">
                        <div class="kpi-bloque-interno p-3 h-100">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div class="kpi-icono-cuadro kpi-icono-salvia">
                                    <i class="bi bi-headset"></i>
                                </div>
                                <div class="small fw-bold text-hrc-primario lh-sm">Admisión Activa</div>
                            </div>
                            <div class="d-flex align-items-baseline gap-2">
                                <span class="h3 fw-bold text-hrc-primario mb-0">{{ $admisionActivosCount }}</span>
                                <span class="texto-xs text-muted">/ {{ $totalAdmision }} registrados</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-lg-3">
                        <div class="kpi-bloque-interno p-3 h-100">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div class="kpi-icono-cuadro kpi-icono-terracota">
                                    <i class="bi bi-people"></i>
                                </div>
                                <div class="small fw-bold text-hrc-primario lh-sm">Cuentas Habilitadas</div>
                            </div>
                            <div class="d-flex align-items-baseline gap-2">
                                <span class="h3 fw-bold text-hrc-primario mb-0">{{ $totalActivosGlobal }}</span>
                                <span class="texto-xs text-muted">de {{ $totalPersonal }} totales</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-lg-3">
                        <div class="kpi-bloque-interno p-3 h-100">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div class="kpi-icono-cuadro kpi-icono-ocre">
                                    <i class="bi bi-calendar2-check"></i>
                                </div>
                                <div class="small fw-bold text-hrc-primario lh-sm">Citas Pendientes</div>
                            </div>
                            <div class="h3 fw-bold text-hrc-primario mb-0">{{ $totalPendientesMedicos }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bloque 1: Personal Médico --}}
            <div class="panel-card-elevada overflow-hidden mb-4">
                <div class="px-4 pt-4 pb-3 border-bottom d-flex flex-column gap-3">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div>
                            <h2 class="h6 fw-bold titulo-panel-acento mb-0">Personal Médico</h2>
                            <span class="texto-xs text-muted">
                                Especialistas habilitados para la asignación automática de turnos
                            </span>
                        </div>

                        @if ($totalMedicos > 0)
                            <div class="buscador-tabla-panel d-flex align-items-center">
                                <i class="bi bi-search text-muted ms-3 small"></i>
                                <input type="text"
                                       id="buscador-admin-medicos"
                                       class="form-control form-control-sm border-0 bg-transparent shadow-none px-2"
                                       placeholder="Buscar médico, DNI o especialidad..."
                                       aria-label="Buscar personal médico">
                            </div>
                        @endif
                    </div>

                    @if ($totalMedicos > 0)
                        <div class="d-flex flex-wrap gap-2" role="group" aria-label="Filtrar médicos por estado">
                            <button type="button" class="tab-estado-panel tab-filtro-medicos activo" data-estado="todos">
                                Todos ({{ $totalMedicos }})
                            </button>
                            <button type="button" class="tab-estado-panel tab-filtro-medicos" data-estado="activo">
                                Activos ({{ $medicosActivosCount }})
                            </button>
                            <button type="button" class="tab-estado-panel tab-filtro-medicos" data-estado="inactivo">
                                Inactivos ({{ $medicosInactivosCount }})
                            </button>
                        </div>
                    @endif
                </div>

                <div class="table-responsive">
                    <table class="table tabla-panel-suave align-middle mb-0 small">
                        <thead>
                            <tr>
                                <th class="ps-4">Médico</th>
                                <th>Especialidad / Consultorio</th>
                                <th>Contacto</th>
                                <th class="text-center">Pendientes</th>
                                <th class="text-center">Estado</th>
                                <th class="text-end pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($medicos as $medico)
                                @php
                                    $u = $medico->user;
                                    $inicialesMed = mb_strtoupper(
                                        mb_substr(trim($medico->nombres), 0, 1, 'UTF-8') .
                                        mb_substr(trim($medico->apellidos), 0, 1, 'UTF-8'),
                                        'UTF-8'
                                    );
                                    $espNombre = $medico->especialidad->nombre ?? 'Sin asignar';
                                    $indiceBusqueda = trim("{$medico->nombres} {$medico->apellidos} {$u->numero_documento} {$espNombre} {$medico->consultorio}");
                                    $fechaNacIso = $u->fecha_nacimiento ? \Carbon\Carbon::parse($u->fecha_nacimiento)->format('Y-m-d') : '';
                                @endphp
                                <tr class="fila-admin-medico {{ $medico->activo ? '' : 'fila-cita-inactiva' }}"
                                    data-estado="{{ $medico->activo ? 'activo' : 'inactivo' }}"
                                    data-busqueda="{{ $indiceBusqueda }}">
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-mini-panel flex-shrink-0">{{ $inicialesMed }}</div>
                                            <div class="min-w-0">
                                                <div class="fw-bold {{ $medico->activo ? 'text-hrc-primario' : 'text-muted' }}">
                                                    Dr. {{ $medico->nombres }} {{ $medico->apellidos }}
                                                </div>
                                                <div class="texto-xs text-muted">
                                                    {{ $u->tipo_documento }}: {{ $u->numero_documento }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $espNombre }}</div>
                                        <div class="texto-xs text-muted">{{ $medico->consultorio }}</div>
                                    </td>
                                    <td>
                                        <div class="text-dark">{{ $u->correo ?: 'Sin correo' }}</div>
                                        <div class="texto-xs text-muted">{{ $u->telefono ?: 'Sin teléfono' }}</div>
                                    </td>
                                    <td class="text-center">
                                        @if ($medico->citas_pendientes_count > 0)
                                            <span class="ticket-pill-estado badge-agenda-pendiente py-1 px-2 texto-xs">
                                                {{ $medico->citas_pendientes_count }}
                                            </span>
                                        @else
                                            <span class="text-muted">0</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($medico->activo)
                                            <span class="ticket-pill-estado badge-agenda-atendido py-1 px-2 texto-xs">Activo</span>
                                        @else
                                            <span class="etiqueta-sin-cupos-mate py-1 px-2 texto-xs">Inactivo</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4 text-nowrap">
                                        <div class="d-inline-flex align-items-center gap-2">
                                            <button type="button"
                                                    class="btn btn-icono-panel btn-sm btn-editar-personal"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalEditarPersonal"
                                                    data-action="{{ route('admin.personal.update', $u) }}"
                                                    data-rol="medico"
                                                    data-tipo-documento="{{ $u->tipo_documento }}"
                                                    data-numero-documento="{{ $u->numero_documento }}"
                                                    data-nombres="{{ $u->nombres }}"
                                                    data-apellido-paterno="{{ $u->apellido_paterno }}"
                                                    data-apellido-materno="{{ $u->apellido_materno }}"
                                                    data-fecha-nacimiento="{{ $fechaNacIso }}"
                                                    data-direccion="{{ $u->direccion }}"
                                                    data-telefono="{{ $u->telefono }}"
                                                    data-correo="{{ $u->correo }}"
                                                    data-especialidad-id="{{ $medico->especialidad_id }}"
                                                    data-consultorio="{{ $medico->consultorio }}">
                                                <i class="bi bi-pencil-square me-1"></i>Editar
                                            </button>

                                            @if ($medico->activo)
                                                <button type="button"
                                                        class="btn btn-cancelar-mate btn-sm rounded-pill px-3 btn-abrir-desactivar-personal"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalConfirmarDesactivarPersonal"
                                                        data-action="{{ route('admin.personal.toggle', $u) }}"
                                                        data-nombre="Dr. {{ $medico->nombres }} {{ $medico->apellidos }}"
                                                        data-rol="medico"
                                                        data-pendientes="{{ $medico->citas_pendientes_count }}">
                                                    Desactivar
                                                </button>
                                            @else
                                                <form action="{{ route('admin.personal.toggle', $u) }}" method="POST" class="m-0">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-activar-mate btn-sm rounded-pill px-3">
                                                        Activar
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        No hay médicos registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div id="sin-resultados-medicos-admin" class="text-center py-4 text-muted small d-none">
                    No se encontraron médicos que coincidan con el filtro aplicado.
                </div>
            </div>

            {{-- Bloque 2: Personal de Admisión --}}
            <div class="panel-card-elevada overflow-hidden">
                <div class="px-4 pt-4 pb-3 border-bottom d-flex flex-column gap-3">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div>
                            <h2 class="h6 fw-bold titulo-panel-acento mb-0">Personal de Admisión</h2>
                            <span class="texto-xs text-muted">
                                Operadores autorizados para consulta de agendas y gestión en ventanilla
                            </span>
                        </div>

                        @if ($totalAdmision > 0)
                            <div class="buscador-tabla-panel d-flex align-items-center">
                                <i class="bi bi-search text-muted ms-3 small"></i>
                                <input type="text"
                                       id="buscador-admin-admision"
                                       class="form-control form-control-sm border-0 bg-transparent shadow-none px-2"
                                       placeholder="Buscar personal o DNI..."
                                       aria-label="Buscar personal de admisión">
                            </div>
                        @endif
                    </div>

                    @if ($totalAdmision > 0)
                        <div class="d-flex flex-wrap gap-2" role="group" aria-label="Filtrar personal de admisión por estado">
                            <button type="button" class="tab-estado-panel tab-filtro-admision activo" data-estado="todos">
                                Todos ({{ $totalAdmision }})
                            </button>
                            <button type="button" class="tab-estado-panel tab-filtro-admision" data-estado="activo">
                                Activos ({{ $admisionActivosCount }})
                            </button>
                            <button type="button" class="tab-estado-panel tab-filtro-admision" data-estado="inactivo">
                                Inactivos ({{ $admisionInactivosCount }})
                            </button>
                        </div>
                    @endif
                </div>

                <div class="table-responsive">
                    <table class="table tabla-panel-suave align-middle mb-0 small">
                        <thead>
                            <tr>
                                <th class="ps-4">Personal</th>
                                <th>Contacto</th>
                                <th>Dirección</th>
                                <th class="text-center">Estado</th>
                                <th class="text-end pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($personalAdmision as $adm)
                                @php
                                    $inicialesAdm = mb_strtoupper(
                                        mb_substr(trim($adm->nombres), 0, 1, 'UTF-8') .
                                        mb_substr(trim($adm->apellido_paterno), 0, 1, 'UTF-8'),
                                        'UTF-8'
                                    );
                                    $indiceBusquedaAdm = trim("{$adm->nombres} {$adm->apellido_paterno} {$adm->apellido_materno} {$adm->numero_documento} {$adm->correo}");
                                    $fechaNacAdmIso = $adm->fecha_nacimiento ? \Carbon\Carbon::parse($adm->fecha_nacimiento)->format('Y-m-d') : '';
                                @endphp
                                <tr class="fila-admin-admision {{ $adm->activo ? '' : 'fila-cita-inactiva' }}"
                                    data-estado="{{ $adm->activo ? 'activo' : 'inactivo' }}"
                                    data-busqueda="{{ $indiceBusquedaAdm }}">
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-mini-panel flex-shrink-0">{{ $inicialesAdm }}</div>
                                            <div class="min-w-0">
                                                <div class="fw-bold {{ $adm->activo ? 'text-hrc-primario' : 'text-muted' }}">
                                                    {{ $adm->nombres }} {{ $adm->apellido_paterno }} {{ $adm->apellido_materno }}
                                                </div>
                                                <div class="texto-xs text-muted">
                                                    {{ $adm->tipo_documento }}: {{ $adm->numero_documento }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-dark">{{ $adm->correo ?: 'Sin correo' }}</div>
                                        <div class="texto-xs text-muted">{{ $adm->telefono ?: 'Sin teléfono' }}</div>
                                    </td>
                                    <td class="text-muted">{{ $adm->direccion }}</td>
                                    <td class="text-center">
                                        @if ($adm->activo)
                                            <span class="ticket-pill-estado badge-agenda-atendido py-1 px-2 texto-xs">Activo</span>
                                        @else
                                            <span class="etiqueta-sin-cupos-mate py-1 px-2 texto-xs">Inactivo</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4 text-nowrap">
                                        <div class="d-inline-flex align-items-center gap-2">
                                            <button type="button"
                                                    class="btn btn-icono-panel btn-sm btn-editar-personal"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalEditarPersonal"
                                                    data-action="{{ route('admin.personal.update', $adm) }}"
                                                    data-rol="admision"
                                                    data-tipo-documento="{{ $adm->tipo_documento }}"
                                                    data-numero-documento="{{ $adm->numero_documento }}"
                                                    data-nombres="{{ $adm->nombres }}"
                                                    data-apellido-paterno="{{ $adm->apellido_paterno }}"
                                                    data-apellido-materno="{{ $adm->apellido_materno }}"
                                                    data-fecha-nacimiento="{{ $fechaNacAdmIso }}"
                                                    data-direccion="{{ $adm->direccion }}"
                                                    data-telefono="{{ $adm->telefono }}"
                                                    data-correo="{{ $adm->correo }}">
                                                <i class="bi bi-pencil-square me-1"></i>Editar
                                            </button>

                                            @if ($adm->activo)
                                                <button type="button"
                                                        class="btn btn-cancelar-mate btn-sm rounded-pill px-3 btn-abrir-desactivar-personal"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalConfirmarDesactivarPersonal"
                                                        data-action="{{ route('admin.personal.toggle', $adm) }}"
                                                        data-nombre="{{ $adm->nombres }} {{ $adm->apellido_paterno }}"
                                                        data-rol="admision"
                                                        data-pendientes="0">
                                                    Desactivar
                                                </button>
                                            @else
                                                <form action="{{ route('admin.personal.toggle', $adm) }}" method="POST" class="m-0">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-activar-mate btn-sm rounded-pill px-3">
                                                        Activar
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        No hay personal de admisión registrado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div id="sin-resultados-admision-admin" class="text-center py-4 text-muted small d-none">
                    No se encontró personal de admisión que coincida con el filtro aplicado.
                </div>
            </div>

        </div>
    </section>

    {{-- Modal de registro de nuevo personal --}}
    <div class="modal fade" id="modalCrearPersonal" tabindex="-1" aria-labelledby="modalCrearPersonalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow modal-mate">
                <form id="formCrearPersonal" action="{{ route('admin.personal.store') }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                        <h5 class="modal-title fw-bold text-hrc-primario" id="modalCrearPersonalLabel">
                            Registrar Nuevo Personal
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body px-4 py-3">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="crear_rol" class="form-label small fw-bold text-hrc-primario">Rol</label>
                                <select id="crear_rol" name="rol" class="form-select input-mate" required>
                                    <option value="medico" {{ old('rol', 'medico') === 'medico' ? 'selected' : '' }}>Médico</option>
                                    <option value="admision" {{ old('rol') === 'admision' ? 'selected' : '' }}>Admisión</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="crear_tipo_documento" class="form-label small fw-bold text-hrc-primario">Tipo Documento</label>
                                <select id="crear_tipo_documento" name="tipo_documento" class="form-select input-mate" required>
                                    <option value="DNI" {{ old('tipo_documento', 'DNI') === 'DNI' ? 'selected' : '' }}>DNI</option>
                                    <option value="CE" {{ old('tipo_documento') === 'CE' ? 'selected' : '' }}>Carnet de Extranjería</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="crear_numero_documento" class="form-label small fw-bold text-hrc-primario">Número Documento</label>
                                <input type="text" id="crear_numero_documento" name="numero_documento" value="{{ old('numero_documento') }}" class="form-control input-mate" maxlength="20" required>
                            </div>

                            <div class="col-md-4">
                                <label for="crear_nombres" class="form-label small fw-bold text-hrc-primario">Nombres</label>
                                <input type="text" id="crear_nombres" name="nombres" value="{{ old('nombres') }}" class="form-control input-mate" maxlength="100" required>
                            </div>
                            <div class="col-md-4">
                                <label for="crear_apellido_paterno" class="form-label small fw-bold text-hrc-primario">Apellido Paterno</label>
                                <input type="text" id="crear_apellido_paterno" name="apellido_paterno" value="{{ old('apellido_paterno') }}" class="form-control input-mate" maxlength="100" required>
                            </div>
                            <div class="col-md-4">
                                <label for="crear_apellido_materno" class="form-label small fw-bold text-hrc-primario">Apellido Materno</label>
                                <input type="text" id="crear_apellido_materno" name="apellido_materno" value="{{ old('apellido_materno') }}" class="form-control input-mate" maxlength="100" required>
                            </div>

                            <div class="col-md-4">
                                <label for="crear_fecha_nacimiento" class="form-label small fw-bold text-hrc-primario">Fecha Nacimiento</label>
                                <input type="date" id="crear_fecha_nacimiento" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}" class="form-control input-mate" required>
                            </div>
                            <div class="col-md-4">
                                <label for="crear_telefono" class="form-label small fw-bold text-hrc-primario">Teléfono</label>
                                <input type="text" id="crear_telefono" name="telefono" value="{{ old('telefono') }}" class="form-control input-mate" maxlength="15">
                            </div>
                            <div class="col-md-4">
                                <label for="crear_correo" class="form-label small fw-bold text-hrc-primario">Correo Institucional</label>
                                <input type="email" id="crear_correo" name="correo" value="{{ old('correo') }}" class="form-control input-mate" maxlength="255">
                            </div>

                            <div class="col-md-7">
                                <label for="crear_direccion" class="form-label small fw-bold text-hrc-primario">Dirección</label>
                                <input type="text" id="crear_direccion" name="direccion" value="{{ old('direccion') }}" class="form-control input-mate" maxlength="255" required>
                            </div>
                            <div class="col-md-5">
                                <label for="crear_password" class="form-label small fw-bold text-hrc-primario">
                                    Contraseña Inicial <span class="fw-normal text-muted">(Opcional)</span>
                                </label>
                                <div class="campo-password-mate position-relative">
                                    <input type="password"
                                           id="crear_password"
                                           name="password"
                                           class="form-control input-mate input-password-mate"
                                           minlength="6"
                                           placeholder="Por defecto: N° documento">
                                    <button type="button"
                                            class="btn-ojo-interno btn-toggle-password"
                                            data-target="crear_password"
                                            aria-label="Mostrar contraseña"
                                            aria-pressed="false">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div id="campos-medico-crear" class="resumen-reserva-mate p-3 mt-4">
                            <h6 class="fw-bold text-hrc-primario small mb-3">
                                <i class="bi bi-hospital me-1 text-hrc-acento"></i>Asignación Clínica
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="crear_especialidad_id" class="form-label small fw-bold text-hrc-primario">Especialidad</label>
                                    <select id="crear_especialidad_id" name="especialidad_id" class="form-select input-mate">
                                        <option value="">Seleccione especialidad</option>
                                        @foreach ($especialidades as $esp)
                                            <option value="{{ $esp->id }}" {{ old('especialidad_id') == $esp->id ? 'selected' : '' }} {{ $esp->activo ? '' : 'disabled' }}>
                                                {{ $esp->nombre }} {{ $esp->activo ? '' : '(Inactiva)' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="crear_consultorio" class="form-label small fw-bold text-hrc-primario">Consultorio</label>
                                    <input type="text" id="crear_consultorio" name="consultorio" value="{{ old('consultorio') }}" class="form-control input-mate" maxlength="100" placeholder="Ej: Consultorio 10 - Piso 2">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 px-4 pb-4 pt-2">
                        <button type="button" class="btn btn-outline-hrc-primario rounded-pill px-4" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-hrc-acento rounded-pill px-4">
                            Guardar Personal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal reutilizable de edición de personal --}}
    <div class="modal fade" id="modalEditarPersonal" tabindex="-1" aria-labelledby="modalEditarPersonalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow modal-mate">
                <form id="formEditarPersonal" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                        <h5 class="modal-title fw-bold text-hrc-primario d-flex align-items-center gap-2" id="modalEditarPersonalLabel">
                            <span>Editar Personal</span>
                            <span id="editar_badge_rol" class="ticket-pill-estado badge-estado-atendido texto-xs"></span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body px-4 py-3">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="editar_tipo_documento" class="form-label small fw-bold text-hrc-primario">Tipo Documento</label>
                                <select id="editar_tipo_documento" name="tipo_documento" class="form-select input-mate" required>
                                    <option value="DNI">DNI</option>
                                    <option value="CE">Carnet de Extranjería</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="editar_numero_documento" class="form-label small fw-bold text-hrc-primario">Número Documento</label>
                                <input type="text" id="editar_numero_documento" name="numero_documento" class="form-control input-mate" maxlength="20" required>
                            </div>

                            <div class="col-md-4">
                                <label for="editar_nombres" class="form-label small fw-bold text-hrc-primario">Nombres</label>
                                <input type="text" id="editar_nombres" name="nombres" class="form-control input-mate" maxlength="100" required>
                            </div>
                            <div class="col-md-4">
                                <label for="editar_apellido_paterno" class="form-label small fw-bold text-hrc-primario">Apellido Paterno</label>
                                <input type="text" id="editar_apellido_paterno" name="apellido_paterno" class="form-control input-mate" maxlength="100" required>
                            </div>
                            <div class="col-md-4">
                                <label for="editar_apellido_materno" class="form-label small fw-bold text-hrc-primario">Apellido Materno</label>
                                <input type="text" id="editar_apellido_materno" name="apellido_materno" class="form-control input-mate" maxlength="100" required>
                            </div>

                            <div class="col-md-4">
                                <label for="editar_fecha_nacimiento" class="form-label small fw-bold text-hrc-primario">Fecha Nacimiento</label>
                                <input type="date" id="editar_fecha_nacimiento" name="fecha_nacimiento" class="form-control input-mate" required>
                            </div>
                            <div class="col-md-4">
                                <label for="editar_telefono" class="form-label small fw-bold text-hrc-primario">Teléfono</label>
                                <input type="text" id="editar_telefono" name="telefono" class="form-control input-mate" maxlength="15">
                            </div>
                            <div class="col-md-4">
                                <label for="editar_correo" class="form-label small fw-bold text-hrc-primario">Correo Institucional</label>
                                <input type="email" id="editar_correo" name="correo" class="form-control input-mate" maxlength="255">
                            </div>

                            <div class="col-md-7">
                                <label for="editar_direccion" class="form-label small fw-bold text-hrc-primario">Dirección</label>
                                <input type="text" id="editar_direccion" name="direccion" class="form-control input-mate" maxlength="255" required>
                            </div>
                            <div class="col-md-5">
                                <label for="editar_password" class="form-label small fw-bold text-hrc-primario">
                                    Nueva Contraseña <span class="fw-normal text-muted">(Opcional)</span>
                                </label>
                                <div class="campo-password-mate position-relative">
                                    <input type="password"
                                           id="editar_password"
                                           name="password"
                                           class="form-control input-mate input-password-mate"
                                           minlength="6"
                                           placeholder="Vacío para mantener actual">
                                    <button type="button"
                                            class="btn-ojo-interno btn-toggle-password"
                                            data-target="editar_password"
                                            aria-label="Mostrar contraseña"
                                            aria-pressed="false">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div id="campos-medico-editar" class="resumen-reserva-mate p-3 mt-4">
                            <h6 class="fw-bold text-hrc-primario small mb-3">
                                <i class="bi bi-hospital me-1 text-hrc-acento"></i>Asignación Clínica
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="editar_especialidad_id" class="form-label small fw-bold text-hrc-primario">Especialidad</label>
                                    <select id="editar_especialidad_id" name="especialidad_id" class="form-select input-mate">
                                        <option value="">Seleccione especialidad</option>
                                        @foreach ($especialidades as $esp)
                                            <option value="{{ $esp->id }}" data-activa="{{ $esp->activo ? '1' : '0' }}">
                                                {{ $esp->nombre }} {{ $esp->activo ? '' : '(Inactiva)' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="editar_consultorio" class="form-label small fw-bold text-hrc-primario">Consultorio</label>
                                    <input type="text" id="editar_consultorio" name="consultorio" class="form-control input-mate" maxlength="100">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 px-4 pb-4 pt-2">
                        <button type="button" class="btn btn-outline-hrc-primario rounded-pill px-4" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-hrc-acento rounded-pill px-4">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal de confirmación para baja lógica de cuentas de personal --}}
    <div class="modal fade" id="modalConfirmarDesactivarPersonal" tabindex="-1" aria-labelledby="modalDesactivarPersonalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow modal-mate">
                <form id="formDesactivarPersonal" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                        <h5 class="modal-title fw-bold text-hrc-primario" id="modalDesactivarPersonalLabel">
                            ¿Desactivar cuenta de personal?
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body px-4 py-3 small text-muted">
                        <p class="mb-3" id="modal-desactivar-personal-texto">
                            Se bloqueará el acceso al sistema para <strong id="modal-desactivar-personal-nombre" class="text-hrc-primario">—</strong>.
                        </p>
                        <div id="aviso-pendientes-personal" class="alerta-mate-aviso p-3 d-none">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            Este profesional registra <strong id="modal-desactivar-personal-pendientes">0</strong> citas pendientes que deberán atenderse o gestionarse desde Admisión.
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 px-4 pb-4 pt-2">
                        <button type="button" class="btn btn-outline-hrc-primario rounded-pill px-4" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button type="submit" id="btn-confirmar-desactivar-personal" class="btn btn-hrc-acento rounded-pill px-4">
                            Sí, desactivar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('js/admin/personal.js') }}"></script>
@endsection