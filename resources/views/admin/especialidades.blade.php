@extends('layouts.main')

@section('titulo', 'Gestión de Especialidades')
@section('clase_main', 'p-0')

@section('contenido')
    @php
        $totalEspecialidades = $especialidades->count();
        $activasCount = $especialidades->where('activo', true)->count();
        $inactivasCount = $totalEspecialidades - $activasCount;
        $totalMedicosActivos = $especialidades->sum('medicos_activos_count');
        $totalCitasPendientes = $especialidades->sum('citas_pendientes_count');
    @endphp

    <section class="cabecera-interna-mate seccion-pizarra py-4">
        <div class="container py-2">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                <div>
                    <span class="sobretitulo-mate d-block mb-1">Administración del catálogo</span>
                    <h1 class="h3 fw-bold text-hrc-primario mb-1">Gestión de Especialidades</h1>
                    <p class="text-muted small mb-0">
                        Catálogo clínico habilitado para la programación de citas en consulta externa.
                    </p>
                </div>

                <div>
                    <button type="button"
                            class="btn btn-hrc-acento rounded-pill px-4"
                            data-bs-toggle="modal"
                            data-bs-target="#modalCrearEspecialidad">
                        <i class="bi bi-plus-lg me-1"></i> Nueva Especialidad
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

            {{-- Resumen compacto del catálogo clínico --}}
            <div class="panel-card-elevada p-4 mb-4">
                <div class="row g-3">
                    <div class="col-6 col-lg-3">
                        <div class="kpi-bloque-interno p-3 h-100">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div class="kpi-icono-cuadro kpi-icono-azul">
                                    <i class="bi bi-grid"></i>
                                </div>
                                <div class="small fw-bold text-hrc-primario lh-sm">Registradas</div>
                            </div>
                            <div class="h3 fw-bold text-hrc-primario mb-0">{{ $totalEspecialidades }}</div>
                        </div>
                    </div>

                    <div class="col-6 col-lg-3">
                        <div class="kpi-bloque-interno p-3 h-100">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div class="kpi-icono-cuadro kpi-icono-salvia">
                                    <i class="bi bi-check2-circle"></i>
                                </div>
                                <div class="small fw-bold text-hrc-primario lh-sm">Activas</div>
                            </div>
                            <div class="d-flex align-items-baseline gap-2">
                                <span class="h3 fw-bold text-hrc-primario mb-0">{{ $activasCount }}</span>
                                <span class="texto-xs text-muted">{{ $inactivasCount }} inactivas</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-lg-3">
                        <div class="kpi-bloque-interno p-3 h-100">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div class="kpi-icono-cuadro kpi-icono-terracota">
                                    <i class="bi bi-person-badge"></i>
                                </div>
                                <div class="small fw-bold text-hrc-primario lh-sm">Médicos Activos</div>
                            </div>
                            <div class="h3 fw-bold text-hrc-primario mb-0">{{ $totalMedicosActivos }}</div>
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
                            <div class="h3 fw-bold text-hrc-primario mb-0">{{ $totalCitasPendientes }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Listado principal con filtros locales --}}
            <div class="panel-card-elevada overflow-hidden">
                <div class="px-4 pt-4 pb-3 border-bottom d-flex flex-column gap-3">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div>
                            <h2 class="h6 fw-bold titulo-panel-acento mb-0">Catálogo de Especialidades</h2>
                            <span class="texto-xs text-muted">
                                La imagen y la descripción se muestran a los pacientes en la cartera pública
                            </span>
                        </div>

                        @if ($totalEspecialidades > 0)
                            <div class="buscador-tabla-panel d-flex align-items-center">
                                <i class="bi bi-search text-muted ms-3 small"></i>
                                <input type="text"
                                       id="buscador-admin-especialidades"
                                       class="form-control form-control-sm border-0 bg-transparent shadow-none px-2"
                                       placeholder="Buscar especialidad..."
                                       aria-label="Buscar especialidad">
                            </div>
                        @endif
                    </div>

                    @if ($totalEspecialidades > 0)
                        <div class="d-flex flex-wrap gap-2" role="group" aria-label="Filtrar especialidades por estado">
                            <button type="button" class="tab-estado-panel activo" data-estado="todas">
                                Todas ({{ $totalEspecialidades }})
                            </button>
                            <button type="button" class="tab-estado-panel" data-estado="activa">
                                Activas ({{ $activasCount }})
                            </button>
                            <button type="button" class="tab-estado-panel" data-estado="inactiva">
                                Inactivas ({{ $inactivasCount }})
                            </button>
                        </div>
                    @endif
                </div>

                <div class="table-responsive">
                    <table class="table tabla-panel-suave align-middle mb-0 small">
                        <thead>
                            <tr>
                                <th class="ps-4">Imagen</th>
                                <th>Especialidad</th>
                                <th>Descripción</th>
                                <th class="text-center">Médicos Activos</th>
                                <th class="text-center">Pendientes</th>
                                <th class="text-center">Estado</th>
                                <th class="text-end pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($especialidades as $esp)
                                @php
                                    $indiceBusqueda = trim("{$esp->nombre} {$esp->descripcion}");
                                @endphp
                                <tr class="fila-admin-especialidad {{ $esp->activo ? '' : 'fila-cita-inactiva' }}"
                                    data-estado="{{ $esp->activo ? 'activa' : 'inactiva' }}"
                                    data-busqueda="{{ $indiceBusqueda }}">
                                    <td class="ps-4">
                                        @if ($esp->imagen)
                                            <img src="{{ asset('storage/' . $esp->imagen) }}"
                                                 alt="Imagen de {{ $esp->nombre }}"
                                                 class="thumb-novedad-admin">
                                        @else
                                            <div class="thumb-novedad-vacia d-flex align-items-center justify-content-center text-muted">
                                                <i class="bi bi-image"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold {{ $esp->activo ? 'text-hrc-primario' : 'text-muted' }}">
                                            {{ $esp->nombre }}
                                        </div>
                                    </td>
                                    <td class="text-muted">
                                        {{ $esp->descripcion ?: 'Sin descripción registrada.' }}
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <span class="fw-bold text-hrc-primario">{{ $esp->medicos_activos_count }}</span>
                                        <span class="text-muted">/ {{ $esp->medicos_count }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if ($esp->citas_pendientes_count > 0)
                                            <span class="ticket-pill-estado badge-agenda-pendiente py-1 px-2 texto-xs">
                                                {{ $esp->citas_pendientes_count }}
                                            </span>
                                        @else
                                            <span class="text-muted">0</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($esp->activo)
                                            <span class="ticket-pill-estado badge-agenda-atendido py-1 px-2 texto-xs">Activa</span>
                                        @else
                                            <span class="etiqueta-sin-cupos-mate py-1 px-2 texto-xs">Inactiva</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4 text-nowrap">
                                        <div class="d-inline-flex align-items-center gap-2">
                                            <button type="button"
                                                    class="btn btn-icono-panel btn-sm btn-editar-especialidad"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalEditarEspecialidad"
                                                    data-action="{{ route('admin.especialidades.update', $esp) }}"
                                                    data-nombre="{{ $esp->nombre }}"
                                                    data-descripcion="{{ $esp->descripcion }}"
                                                    data-imagen-url="{{ $esp->imagen ? asset('storage/' . $esp->imagen) : '' }}">
                                                <i class="bi bi-pencil-square me-1"></i>Editar
                                            </button>

                                            @if ($esp->activo)
                                                <button type="button"
                                                        class="btn btn-cancelar-mate btn-sm rounded-pill px-3 btn-abrir-desactivar-esp"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalConfirmarDesactivarEsp"
                                                        data-action="{{ route('admin.especialidades.toggle', $esp) }}"
                                                        data-nombre="{{ $esp->nombre }}"
                                                        data-pendientes="{{ $esp->citas_pendientes_count }}">
                                                    Desactivar
                                                </button>
                                            @else
                                                <form action="{{ route('admin.especialidades.toggle', $esp) }}" method="POST" class="m-0 form-activar-esp">
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
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        No hay especialidades registradas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div id="sin-resultados-especialidades-admin" class="text-center py-4 text-muted small d-none">
                    No se encontraron especialidades que coincidan con el filtro aplicado.
                </div>
            </div>

        </div>
    </section>

    {{-- Modal para registrar una nueva especialidad --}}
    <div class="modal fade" id="modalCrearEspecialidad" tabindex="-1" aria-labelledby="modalCrearLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow modal-mate">
                <form action="{{ route('admin.especialidades.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                        <h5 class="modal-title fw-bold text-hrc-primario" id="modalCrearLabel">Nueva Especialidad</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body px-4 py-3">
                        <div class="mb-3">
                            <label for="crear_nombre" class="form-label small fw-bold text-hrc-primario">Nombre</label>
                            <input type="text"
                                   id="crear_nombre"
                                   name="nombre"
                                   value="{{ old('nombre') }}"
                                   class="form-control input-mate"
                                   maxlength="100"
                                   required
                                   placeholder="Ej: Dermatología">
                        </div>
                        <div class="mb-3">
                            <label for="crear_descripcion" class="form-label small fw-bold text-hrc-primario">
                                Descripción <span class="fw-normal text-muted">(Opcional)</span>
                            </label>
                            <textarea id="crear_descripcion"
                                      name="descripcion"
                                      class="form-control input-mate"
                                      rows="3"
                                      maxlength="500"
                                      placeholder="Breve descripción del área clínica visible para los pacientes">{{ old('descripcion') }}</textarea>
                        </div>
                        <div class="mb-0">
                            <label for="crear_imagen" class="form-label small fw-bold text-hrc-primario">
                                Imagen de referencia <span class="fw-normal text-muted">(Opcional)</span>
                            </label>
                            <input type="file"
                                   id="crear_imagen"
                                   name="imagen"
                                   class="form-control input-mate"
                                   accept="image/jpeg,image/png,image/webp">
                            <div class="form-text texto-xs">Formatos JPG, PNG o WEBP. Recomendado horizontal, máx. 2 MB.</div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 px-4 pb-4 pt-2">
                        <button type="button" class="btn btn-outline-hrc-primario rounded-pill px-4" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-hrc-acento rounded-pill px-4">
                            Guardar Especialidad
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal para actualizar datos e imagen de referencia --}}
    <div class="modal fade" id="modalEditarEspecialidad" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow modal-mate">
                <form id="formEditarEspecialidad" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                        <h5 class="modal-title fw-bold text-hrc-primario" id="modalEditarLabel">Editar Especialidad</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body px-4 py-3">
                        <div class="mb-3">
                            <label for="editar_nombre" class="form-label small fw-bold text-hrc-primario">Nombre</label>
                            <input type="text" id="editar_nombre" name="nombre" class="form-control input-mate" maxlength="100" required>
                        </div>
                        <div class="mb-3">
                            <label for="editar_descripcion" class="form-label small fw-bold text-hrc-primario">
                                Descripción <span class="fw-normal text-muted">(Opcional)</span>
                            </label>
                            <textarea id="editar_descripcion" name="descripcion" class="form-control input-mate" rows="3" maxlength="500"></textarea>
                        </div>

                        <div id="bloque-imagen-esp-actual" class="mb-3" style="display: none;">
                            <div class="resumen-reserva-mate p-3 d-flex align-items-center gap-3">
                                <img id="preview-imagen-esp-actual" src="" alt="Imagen actual" class="thumb-novedad-admin">
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="checkbox" id="editar_eliminar_imagen_esp" name="eliminar_imagen" value="1">
                                    <label class="form-check-label small fw-bold text-hrc-acento" for="editar_eliminar_imagen_esp">
                                        Quitar imagen actual
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-0">
                            <label for="editar_imagen" class="form-label small fw-bold text-hrc-primario">
                                Reemplazar imagen <span class="fw-normal text-muted">(Opcional)</span>
                            </label>
                            <input type="file"
                                   id="editar_imagen"
                                   name="imagen"
                                   class="form-control input-mate"
                                   accept="image/jpeg,image/png,image/webp">
                            <div class="form-text texto-xs">Formatos JPG, PNG o WEBP. Máximo 2 MB.</div>
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

    {{-- Modal de confirmación para desactivación lógica --}}
    <div class="modal fade" id="modalConfirmarDesactivarEsp" tabindex="-1" aria-labelledby="modalDesactivarEspLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow modal-mate">
                <form id="formDesactivarEspecialidad" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                        <h5 class="modal-title fw-bold text-hrc-primario" id="modalDesactivarEspLabel">
                            ¿Desactivar especialidad?
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body px-4 py-3 small text-muted">
                        <p class="mb-3">
                            La especialidad <strong id="modal-desactivar-esp-nombre" class="text-hrc-primario">—</strong> dejará de mostrarse en la cartera pública y en el formulario de nuevas reservas.
                        </p>
                        <div id="aviso-pendientes-esp" class="alerta-mate-aviso p-3 d-none">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            Actualmente registra <strong id="modal-desactivar-esp-pendientes">0</strong> citas pendientes que deberán seguir su atención o gestionarse desde Admisión.
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 px-4 pb-4 pt-2">
                        <button type="button" class="btn btn-outline-hrc-primario rounded-pill px-4" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button type="submit" id="btn-confirmar-desactivar-esp" class="btn btn-hrc-acento rounded-pill px-4">
                            Sí, desactivar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('js/admin/especialidades.js') }}"></script>
@endsection