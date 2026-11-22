@extends('layouts.main')

@section('titulo', 'Gestión de Médicos y Personal')

@section('contenido')
<div class="container">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold text-hrc-primario mb-1">Médicos y Personal de Admisión</h2>
            <p class="text-muted mb-0">Administración de cuentas operativas, consultorios y estados de acceso</p>
        </div>
        <div>
            <button type="button" class="btn btn-hrc-acento px-4" data-bs-toggle="modal" data-bs-target="#modalCrearPersonal">
                + Registrar Personal
            </button>
        </div>
    </div>

    @include('admin._nav')

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    @if (session('warning'))
        <div class="alert alert-warning alert-dismissible fade show shadow-sm mb-4" role="alert">
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
            <strong class="d-block mb-1">Revise los datos ingresados:</strong>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    {{-- Tabla de personal médico con conteo de citas pendientes vigentes --}}
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-hrc-primario mb-0">Personal Médico</h5>
            <span class="badge bg-primary">{{ $medicos->where('activo', true)->count() }} activos / {{ $medicos->count() }} total</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary small text-uppercase">
                    <tr>
                        <th class="ps-4 py-3">Médico</th>
                        <th class="py-3">Especialidad / Consultorio</th>
                        <th class="py-3">Contacto</th>
                        <th class="text-center py-3">Citas Pend.</th>
                        <th class="text-center py-3">Estado</th>
                        <th class="text-end pe-4 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($medicos as $medico)
                        @php $u = $medico->user; @endphp
                        <tr class="{{ $medico->activo ? '' : 'table-light text-muted' }}">
                            <td class="ps-4 py-3">
                                <div class="fw-bold {{ $medico->activo ? 'text-hrc-primario' : 'text-muted' }}">
                                    Dr. {{ $medico->nombres }} {{ $medico->apellidos }}
                                </div>
                                <div class="small text-muted">{{ $u->tipo_documento }}: {{ $u->numero_documento }}</div>
                            </td>
                            <td class="py-3">
                                <div class="fw-bold small">{{ $medico->especialidad->nombre ?? 'Sin asignar' }}</div>
                                <div class="small text-muted">{{ $medico->consultorio }}</div>
                            </td>
                            <td class="py-3 small">
                                <div>{{ $u->correo ?: 'Sin correo' }}</div>
                                <div class="text-muted">{{ $u->telefono ?: 'Sin teléfono' }}</div>
                            </td>
                            <td class="text-center py-3">
                                @if ($medico->citas_pendientes_count > 0)
                                    <span class="badge bg-warning text-dark">{{ $medico->citas_pendientes_count }}</span>
                                @else
                                    <span class="text-muted small">0</span>
                                @endif
                            </td>
                            <td class="text-center py-3">
                                @if ($medico->activo)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-secondary">Inactivo</span>
                                @endif
                            </td>
                            <td class="text-end pe-4 py-3">
                                <div class="d-inline-flex gap-2">
                                    <button type="button"
                                            class="btn btn-outline-primary btn-sm btn-editar-personal"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEditarPersonal"
                                            data-action="{{ route('admin.personal.update', $u) }}"
                                            data-rol="medico"
                                            data-tipo-documento="{{ $u->tipo_documento }}"
                                            data-numero-documento="{{ $u->numero_documento }}"
                                            data-nombres="{{ $u->nombres }}"
                                            data-apellido-paterno="{{ $u->apellido_paterno }}"
                                            data-apellido-materno="{{ $u->apellido_materno }}"
                                            data-fecha-nacimiento="{{ \Carbon\Carbon::parse($u->fecha_nacimiento)->format('Y-m-d') }}"
                                            data-direccion="{{ $u->direccion }}"
                                            data-telefono="{{ $u->telefono }}"
                                            data-correo="{{ $u->correo }}"
                                            data-especialidad-id="{{ $medico->especialidad_id }}"
                                            data-consultorio="{{ $medico->consultorio }}">
                                        Editar
                                    </button>

                                    <form action="{{ route('admin.personal.toggle', $u) }}"
                                          method="POST"
                                          class="m-0 form-toggle-estado"
                                          data-mensaje="{{ $medico->activo ? '¿Desactivar al Dr. ' . $medico->nombres . ' ' . $medico->apellidos . '? Dejará de recibir nuevas citas y se bloqueará su acceso.' : '' }}">
                                        @csrf
                                        @method('PATCH')
                                        @if ($medico->activo)
                                            <button type="submit" class="btn btn-outline-danger btn-sm">Desactivar</button>
                                        @else
                                            <button type="submit" class="btn btn-outline-success btn-sm">Activar</button>
                                        @endif
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No hay médicos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Tabla de personal de ventanilla y admisión --}}
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-hrc-primario mb-0">Personal de Admisión</h5>
            <span class="badge bg-secondary">{{ $personalAdmision->where('activo', true)->count() }} activos / {{ $personalAdmision->count() }} total</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary small text-uppercase">
                    <tr>
                        <th class="ps-4 py-3">Personal</th>
                        <th class="py-3">Contacto</th>
                        <th class="py-3">Dirección</th>
                        <th class="text-center py-3">Estado</th>
                        <th class="text-end pe-4 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($personalAdmision as $adm)
                        <tr class="{{ $adm->activo ? '' : 'table-light text-muted' }}">
                            <td class="ps-4 py-3">
                                <div class="fw-bold {{ $adm->activo ? 'text-hrc-primario' : 'text-muted' }}">
                                    {{ $adm->nombres }} {{ $adm->apellido_paterno }} {{ $adm->apellido_materno }}
                                </div>
                                <div class="small text-muted">{{ $adm->tipo_documento }}: {{ $adm->numero_documento }}</div>
                            </td>
                            <td class="py-3 small">
                                <div>{{ $adm->correo ?: 'Sin correo' }}</div>
                                <div class="text-muted">{{ $adm->telefono ?: 'Sin teléfono' }}</div>
                            </td>
                            <td class="py-3 small">{{ $adm->direccion }}</td>
                            <td class="text-center py-3">
                                @if ($adm->activo)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-secondary">Inactivo</span>
                                @endif
                            </td>
                            <td class="text-end pe-4 py-3">
                                <div class="d-inline-flex gap-2">
                                    <button type="button"
                                            class="btn btn-outline-primary btn-sm btn-editar-personal"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEditarPersonal"
                                            data-action="{{ route('admin.personal.update', $adm) }}"
                                            data-rol="admision"
                                            data-tipo-documento="{{ $adm->tipo_documento }}"
                                            data-numero-documento="{{ $adm->numero_documento }}"
                                            data-nombres="{{ $adm->nombres }}"
                                            data-apellido-paterno="{{ $adm->apellido_paterno }}"
                                            data-apellido-materno="{{ $adm->apellido_materno }}"
                                            data-fecha-nacimiento="{{ \Carbon\Carbon::parse($adm->fecha_nacimiento)->format('Y-m-d') }}"
                                            data-direccion="{{ $adm->direccion }}"
                                            data-telefono="{{ $adm->telefono }}"
                                            data-correo="{{ $adm->correo }}">
                                        Editar
                                    </button>

                                    <form action="{{ route('admin.personal.toggle', $adm) }}"
                                          method="POST"
                                          class="m-0 form-toggle-estado"
                                          data-mensaje="{{ $adm->activo ? '¿Desactivar la cuenta de ' . $adm->nombres . ' ' . $adm->apellido_paterno . '? Se bloqueará su acceso a ventanilla.' : '' }}">
                                        @csrf
                                        @method('PATCH')
                                        @if ($adm->activo)
                                            <button type="submit" class="btn btn-outline-danger btn-sm">Desactivar</button>
                                        @else
                                            <button type="submit" class="btn btn-outline-success btn-sm">Activar</button>
                                        @endif
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No hay personal de admisión registrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Modal de registro de nuevo personal --}}
<div class="modal fade" id="modalCrearPersonal" tabindex="-1" aria-labelledby="modalCrearPersonalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('admin.personal.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-hrc-primario" id="modalCrearPersonalLabel">Registrar Nuevo Personal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="crear_rol" class="form-label fw-bold text-secondary">Rol</label>
                            <select id="crear_rol" name="rol" class="form-select" required>
                                <option value="medico" {{ old('rol', 'medico') === 'medico' ? 'selected' : '' }}>Médico</option>
                                <option value="admision" {{ old('rol') === 'admision' ? 'selected' : '' }}>Admisión</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="crear_tipo_documento" class="form-label fw-bold text-secondary">Tipo Documento</label>
                            <select id="crear_tipo_documento" name="tipo_documento" class="form-select" required>
                                <option value="DNI" {{ old('tipo_documento', 'DNI') === 'DNI' ? 'selected' : '' }}>DNI</option>
                                <option value="CE" {{ old('tipo_documento') === 'CE' ? 'selected' : '' }}>Carnet de Extranjería</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="crear_numero_documento" class="form-label fw-bold text-secondary">Número Documento</label>
                            <input type="text" id="crear_numero_documento" name="numero_documento" value="{{ old('numero_documento') }}" class="form-control" maxlength="20" required>
                        </div>

                        <div class="col-md-4">
                            <label for="crear_nombres" class="form-label fw-bold text-secondary">Nombres</label>
                            <input type="text" id="crear_nombres" name="nombres" value="{{ old('nombres') }}" class="form-control" maxlength="100" required>
                        </div>
                        <div class="col-md-4">
                            <label for="crear_apellido_paterno" class="form-label fw-bold text-secondary">Apellido Paterno</label>
                            <input type="text" id="crear_apellido_paterno" name="apellido_paterno" value="{{ old('apellido_paterno') }}" class="form-control" maxlength="100" required>
                        </div>
                        <div class="col-md-4">
                            <label for="crear_apellido_materno" class="form-label fw-bold text-secondary">Apellido Materno</label>
                            <input type="text" id="crear_apellido_materno" name="apellido_materno" value="{{ old('apellido_materno') }}" class="form-control" maxlength="100" required>
                        </div>

                        <div class="col-md-4">
                            <label for="crear_fecha_nacimiento" class="form-label fw-bold text-secondary">Fecha Nacimiento</label>
                            <input type="date" id="crear_fecha_nacimiento" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label for="crear_telefono" class="form-label fw-bold text-secondary">Teléfono</label>
                            <input type="text" id="crear_telefono" name="telefono" value="{{ old('telefono') }}" class="form-control" maxlength="15">
                        </div>
                        <div class="col-md-4">
                            <label for="crear_correo" class="form-label fw-bold text-secondary">Correo Institucional</label>
                            <input type="email" id="crear_correo" name="correo" value="{{ old('correo') }}" class="form-control" maxlength="255">
                        </div>

                        <div class="col-md-7">
                            <label for="crear_direccion" class="form-label fw-bold text-secondary">Dirección</label>
                            <input type="text" id="crear_direccion" name="direccion" value="{{ old('direccion') }}" class="form-control" maxlength="255" required>
                        </div>
                        <div class="col-md-5">
                            <label for="crear_password" class="form-label fw-bold text-secondary">Contraseña Inicial (Opcional)</label>
                            <input type="password" id="crear_password" name="password" class="form-control" minlength="6" placeholder="Por defecto: N° de documento">
                        </div>
                    </div>

                    <div id="campos-medico-crear" class="border-top mt-4 pt-3">
                        <h6 class="fw-bold text-hrc-primario mb-3">Asignación Clínica</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="crear_especialidad_id" class="form-label fw-bold text-secondary">Especialidad</label>
                                <select id="crear_especialidad_id" name="especialidad_id" class="form-select">
                                    <option value="">Seleccione especialidad</option>
                                    @foreach ($especialidades as $esp)
                                        <option value="{{ $esp->id }}" {{ old('especialidad_id') == $esp->id ? 'selected' : '' }} {{ $esp->activo ? '' : 'disabled' }}>
                                            {{ $esp->nombre }} {{ $esp->activo ? '' : '(Inactiva)' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="crear_consultorio" class="form-label fw-bold text-secondary">Consultorio</label>
                                <input type="text" id="crear_consultorio" name="consultorio" value="{{ old('consultorio') }}" class="form-control" maxlength="100" placeholder="Ej: Consultorio 10 - Piso 2">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-hrc-acento">Guardar Personal</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal reutilizable de edición de personal --}}
<div class="modal fade" id="modalEditarPersonal" tabindex="-1" aria-labelledby="modalEditarPersonalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="formEditarPersonal" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-hrc-primario" id="modalEditarPersonalLabel">
                        Editar Personal <span id="editar_badge_rol" class="badge bg-secondary ms-2"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="editar_tipo_documento" class="form-label fw-bold text-secondary">Tipo Documento</label>
                            <select id="editar_tipo_documento" name="tipo_documento" class="form-select" required>
                                <option value="DNI">DNI</option>
                                <option value="CE">Carnet de Extranjería</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="editar_numero_documento" class="form-label fw-bold text-secondary">Número Documento</label>
                            <input type="text" id="editar_numero_documento" name="numero_documento" class="form-control" maxlength="20" required>
                        </div>

                        <div class="col-md-4">
                            <label for="editar_nombres" class="form-label fw-bold text-secondary">Nombres</label>
                            <input type="text" id="editar_nombres" name="nombres" class="form-control" maxlength="100" required>
                        </div>
                        <div class="col-md-4">
                            <label for="editar_apellido_paterno" class="form-label fw-bold text-secondary">Apellido Paterno</label>
                            <input type="text" id="editar_apellido_paterno" name="apellido_paterno" class="form-control" maxlength="100" required>
                        </div>
                        <div class="col-md-4">
                            <label for="editar_apellido_materno" class="form-label fw-bold text-secondary">Apellido Materno</label>
                            <input type="text" id="editar_apellido_materno" name="apellido_materno" class="form-control" maxlength="100" required>
                        </div>

                        <div class="col-md-4">
                            <label for="editar_fecha_nacimiento" class="form-label fw-bold text-secondary">Fecha Nacimiento</label>
                            <input type="date" id="editar_fecha_nacimiento" name="fecha_nacimiento" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label for="editar_telefono" class="form-label fw-bold text-secondary">Teléfono</label>
                            <input type="text" id="editar_telefono" name="telefono" class="form-control" maxlength="15">
                        </div>
                        <div class="col-md-4">
                            <label for="editar_correo" class="form-label fw-bold text-secondary">Correo Institucional</label>
                            <input type="email" id="editar_correo" name="correo" class="form-control" maxlength="255">
                        </div>

                        <div class="col-md-7">
                            <label for="editar_direccion" class="form-label fw-bold text-secondary">Dirección</label>
                            <input type="text" id="editar_direccion" name="direccion" class="form-control" maxlength="255" required>
                        </div>
                        <div class="col-md-5">
                            <label for="editar_password" class="form-label fw-bold text-secondary">Nueva Contraseña (Opcional)</label>
                            <input type="password" id="editar_password" name="password" class="form-control" minlength="6" placeholder="Dejar vacío para mantener la actual">
                        </div>
                    </div>

                    <div id="campos-medico-editar" class="border-top mt-4 pt-3">
                        <h6 class="fw-bold text-hrc-primario mb-3">Asignación Clínica</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="editar_especialidad_id" class="form-label fw-bold text-secondary">Especialidad</label>
                                <select id="editar_especialidad_id" name="especialidad_id" class="form-select">
                                    <option value="">Seleccione especialidad</option>
                                    @foreach ($especialidades as $esp)
                                        <option value="{{ $esp->id }}" data-activa="{{ $esp->activo ? '1' : '0' }}">
                                            {{ $esp->nombre }} {{ $esp->activo ? '' : '(Inactiva)' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="editar_consultorio" class="form-label fw-bold text-secondary">Consultorio</label>
                                <input type="text" id="editar_consultorio" name="consultorio" class="form-control" maxlength="100">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-hrc-acento">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
    <script src="{{ asset('js/admin/personal.js') }}"></script>
@endsection