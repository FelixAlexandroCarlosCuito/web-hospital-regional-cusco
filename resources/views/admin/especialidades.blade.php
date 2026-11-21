@extends('layouts.main')

@section('titulo', 'Gestión de Especialidades')

@section('contenido')
<div class="container">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold text-hrc-primario mb-1">Gestión de Especialidades</h2>
            <p class="text-muted mb-0">Catálogo clínico disponible para la reserva de citas en línea</p>
        </div>
        <div>
            <button type="button" class="btn btn-hrc-acento px-4" data-bs-toggle="modal" data-bs-target="#modalCrearEspecialidad">
                + Nueva Especialidad
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

    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary small text-uppercase">
                    <tr>
                        <th class="ps-4 py-3">Especialidad</th>
                        <th class="py-3">Descripción</th>
                        <th class="text-center py-3">Médicos Activos</th>
                        <th class="text-center py-3">Citas Pendientes</th>
                        <th class="text-center py-3">Estado</th>
                        <th class="text-end pe-4 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($especialidades as $esp)
                        <tr class="{{ $esp->activo ? '' : 'table-light text-muted' }}">
                            <td class="ps-4 py-3 fw-bold {{ $esp->activo ? 'text-hrc-primario' : 'text-muted' }}">
                                {{ $esp->nombre }}
                            </td>
                            <td class="py-3 small">
                                {{ $esp->descripcion ?: 'Sin descripción registrada.' }}
                            </td>
                            <td class="text-center py-3">
                                <span class="fw-bold">{{ $esp->medicos_activos_count }}</span>
                                <span class="text-muted small">/ {{ $esp->medicos_count }}</span>
                            </td>
                            <td class="text-center py-3">
                                @if ($esp->citas_pendientes_count > 0)
                                    <span class="badge bg-warning text-dark">{{ $esp->citas_pendientes_count }}</span>
                                @else
                                    <span class="text-muted small">0</span>
                                @endif
                            </td>
                            <td class="text-center py-3">
                                @if ($esp->activo)
                                    <span class="badge bg-success">Activa</span>
                                @else
                                    <span class="badge bg-secondary">Inactiva</span>
                                @endif
                            </td>
                            <td class="text-end pe-4 py-3">
                                <div class="d-inline-flex gap-2">
                                    <button type="button"
                                            class="btn btn-outline-primary btn-sm btn-editar-especialidad"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEditarEspecialidad"
                                            data-action="{{ route('admin.especialidades.update', $esp) }}"
                                            data-nombre="{{ $esp->nombre }}"
                                            data-descripcion="{{ $esp->descripcion }}">
                                        Editar
                                    </button>

                                    <form action="{{ route('admin.especialidades.toggle', $esp) }}" method="POST" class="m-0">
                                        @csrf
                                        @method('PATCH')
                                        @if ($esp->activo)
                                            <button type="submit"
                                                    class="btn btn-outline-danger btn-sm"
                                                    onclick="return confirm('¿Desactivar la especialidad {{ addslashes($esp->nombre) }}? Dejará de mostrarse para nuevas citas.');">
                                                Desactivar
                                            </button>
                                        @else
                                            <button type="submit" class="btn btn-outline-success btn-sm">
                                                Activar
                                            </button>
                                        @endif
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                No hay especialidades registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<div class="modal fade" id="modalCrearEspecialidad" tabindex="-1" aria-labelledby="modalCrearLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('admin.especialidades.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-hrc-primario" id="modalCrearLabel">Nueva Especialidad</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="crear_nombre" class="form-label fw-bold text-secondary">Nombre</label>
                        <input type="text" id="crear_nombre" name="nombre" value="{{ old('nombre') }}" class="form-control" maxlength="100" required placeholder="Ej: Dermatología">
                    </div>
                    <div class="mb-0">
                        <label for="crear_descripcion" class="form-label fw-bold text-secondary">Descripción (Opcional)</label>
                        <textarea id="crear_descripcion" name="descripcion" class="form-control" rows="3" maxlength="500" placeholder="Breve descripción del área clínica para los pacientes">{{ old('descripcion') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-hrc-acento">Guardar Especialidad</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditarEspecialidad" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="formEditarEspecialidad" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-hrc-primario" id="modalEditarLabel">Editar Especialidad</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editar_nombre" class="form-label fw-bold text-secondary">Nombre</label>
                        <input type="text" id="editar_nombre" name="nombre" class="form-control" maxlength="100" required>
                    </div>
                    <div class="mb-0">
                        <label for="editar_descripcion" class="form-label fw-bold text-secondary">Descripción (Opcional)</label>
                        <textarea id="editar_descripcion" name="descripcion" class="form-control" rows="3" maxlength="500"></textarea>
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const formEditar = document.getElementById('formEditarEspecialidad');
        const inputNombre = document.getElementById('editar_nombre');
        const inputDescripcion = document.getElementById('editar_descripcion');

        document.querySelectorAll('.btn-editar-especialidad').forEach(function (boton) {
            boton.addEventListener('click', function () {
                formEditar.action = this.dataset.action;
                inputNombre.value = this.dataset.nombre || '';
                inputDescripcion.value = this.dataset.descripcion || '';
            });
        });
    });
</script>
@endsection