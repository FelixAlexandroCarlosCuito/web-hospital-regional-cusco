@extends('layouts.main')

@section('titulo', 'Gestión de Novedades')

@section('contenido')
<div class="container">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold text-hrc-primario mb-1">Novedades y Comunicados</h2>
            <p class="text-muted mb-0">Publicación de noticias, campañas de salud y avisos del hospital</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('novedades.index') }}" target="_blank" class="btn btn-outline-hrc-primario">
                Ver Portal Público
            </a>
            <button type="button" class="btn btn-hrc-acento px-4" data-bs-toggle="modal" data-bs-target="#modalCrearNovedad">
                + Nueva Novedad
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
                        <th class="ps-4 py-3">Portada</th>
                        <th class="py-3">Título y Resumen</th>
                        <th class="py-3">Categoría</th>
                        <th class="py-3">Fecha</th>
                        <th class="text-center py-3">Estado</th>
                        <th class="text-end pe-4 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($novedades as $novedad)
                        <tr class="{{ $novedad->publicado ? '' : 'table-light text-muted' }}">
                            <td class="ps-4 py-3" style="width: 90px;">
                                @if ($novedad->imagen)
                                    <img src="{{ asset('storage/' . $novedad->imagen) }}"
                                         alt="Portada"
                                         class="rounded"
                                         style="width: 68px; height: 48px; object-fit: cover;">
                                @else
                                    <div class="bg-light border rounded d-flex align-items-center justify-content-center text-muted small"
                                         style="width: 68px; height: 48px;">
                                        Sin img
                                    </div>
                                @endif
                            </td>
                            <td class="py-3">
                                <div class="fw-bold {{ $novedad->publicado ? 'text-hrc-primario' : 'text-muted' }}">
                                    {{ $novedad->titulo }}
                                </div>
                                <div class="small text-muted">{{ \Illuminate\Support\Str::limit($novedad->resumen, 95) }}</div>
                            </td>
                            <td class="py-3">
                                <span class="badge bg-info text-dark">{{ $novedad->categoria }}</span>
                            </td>
                            <td class="py-3 small">
                                @if ($novedad->fecha_publicacion)
                                    {{ $novedad->fecha_publicacion->format('d/m/Y') }}
                                @else
                                    <span class="text-muted">Sin publicar</span>
                                @endif
                            </td>
                            <td class="text-center py-3">
                                @if ($novedad->publicado)
                                    <span class="badge bg-success">Publicado</span>
                                @else
                                    <span class="badge bg-secondary">Borrador</span>
                                @endif
                            </td>
                            <td class="text-end pe-4 py-3">
                                <div class="d-inline-flex gap-1">
                                    <button type="button"
                                            class="btn btn-outline-primary btn-sm btn-editar-novedad"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEditarNovedad"
                                            data-action="{{ route('admin.novedades.update', $novedad) }}"
                                            data-titulo="{{ $novedad->titulo }}"
                                            data-categoria="{{ $novedad->categoria }}"
                                            data-resumen="{{ $novedad->resumen }}"
                                            data-contenido="{{ $novedad->contenido }}"
                                            data-publicado="{{ $novedad->publicado ? '1' : '0' }}"
                                            data-imagen-url="{{ $novedad->imagen ? asset('storage/' . $novedad->imagen) : '' }}">
                                        Editar
                                    </button>

                                    <form action="{{ route('admin.novedades.toggle', $novedad) }}" method="POST" class="m-0">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm {{ $novedad->publicado ? 'btn-outline-secondary' : 'btn-outline-success' }}">
                                            {{ $novedad->publicado ? 'Ocultar' : 'Publicar' }}
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.novedades.destroy', $novedad) }}"
                                          method="POST"
                                          class="m-0 form-eliminar-novedad"
                                          data-titulo="{{ $novedad->titulo }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                Aún no se han registrado novedades ni comunicados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<div class="modal fade" id="modalCrearNovedad" tabindex="-1" aria-labelledby="modalCrearNovedadLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('admin.novedades.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-hrc-primario" id="modalCrearNovedadLabel">Nueva Novedad</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="crear_titulo" class="form-label fw-bold text-secondary">Título</label>
                            <input type="text" id="crear_titulo" name="titulo" value="{{ old('titulo') }}" class="form-control" maxlength="180" required placeholder="Ej: Campaña gratuita de vacunación contra la influenza">
                        </div>
                        <div class="col-md-4">
                            <label for="crear_categoria" class="form-label fw-bold text-secondary">Categoría</label>
                            <select id="crear_categoria" name="categoria" class="form-select" required>
                                @foreach ($categorias as $cat)
                                    <option value="{{ $cat }}" {{ old('categoria') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">
                            <label for="crear_resumen" class="form-label fw-bold text-secondary">Resumen breve</label>
                            <textarea id="crear_resumen" name="resumen" class="form-control" rows="2" maxlength="300" required placeholder="Texto corto que se mostrará en la tarjeta del listado (máx. 300 caracteres)">{{ old('resumen') }}</textarea>
                        </div>

                        <div class="col-12">
                            <label for="crear_contenido" class="form-label fw-bold text-secondary">Contenido completo</label>
                            <textarea id="crear_contenido" name="contenido" class="form-control" rows="6" required placeholder="Detalle completo de la noticia, fechas, requisitos o lugar de atención">{{ old('contenido') }}</textarea>
                        </div>

                        <div class="col-md-8">
                            <label for="crear_imagen" class="form-label fw-bold text-secondary">Imagen de portada (Opcional)</label>
                            <input type="file" id="crear_imagen" name="imagen" class="form-control" accept="image/jpeg,image/png,image/webp">
                            <div class="form-text">Formatos JPG, PNG o WEBP. Máximo 2 MB.</div>
                        </div>

                        <div class="col-md-4 d-flex align-items-center pt-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="crear_publicado" name="publicado" value="1" checked>
                                <label class="form-check-label fw-bold text-secondary" for="crear_publicado">Publicar de inmediato</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-hrc-acento">Guardar Novedad</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditarNovedad" tabindex="-1" aria-labelledby="modalEditarNovedadLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="formEditarNovedad" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-hrc-primario" id="modalEditarNovedadLabel">Editar Novedad</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="editar_titulo" class="form-label fw-bold text-secondary">Título</label>
                            <input type="text" id="editar_titulo" name="titulo" class="form-control" maxlength="180" required>
                        </div>
                        <div class="col-md-4">
                            <label for="editar_categoria" class="form-label fw-bold text-secondary">Categoría</label>
                            <select id="editar_categoria" name="categoria" class="form-select" required>
                                @foreach ($categorias as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">
                            <label for="editar_resumen" class="form-label fw-bold text-secondary">Resumen breve</label>
                            <textarea id="editar_resumen" name="resumen" class="form-control" rows="2" maxlength="300" required></textarea>
                        </div>

                        <div class="col-12">
                            <label for="editar_contenido" class="form-label fw-bold text-secondary">Contenido completo</label>
                            <textarea id="editar_contenido" name="contenido" class="form-control" rows="6" required></textarea>
                        </div>

                        <div class="col-12" id="bloque-imagen-actual" style="display: none;">
                            <div class="d-flex align-items-center gap-3 p-2 border rounded bg-light">
                                <img id="preview-imagen-actual" src="" alt="Portada actual" class="rounded" style="width: 84px; height: 56px; object-fit: cover;">
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="checkbox" id="editar_eliminar_imagen" name="eliminar_imagen" value="1">
                                    <label class="form-check-label small text-danger fw-bold" for="editar_eliminar_imagen">
                                        Quitar imagen actual
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <label for="editar_imagen" class="form-label fw-bold text-secondary">Reemplazar imagen (Opcional)</label>
                            <input type="file" id="editar_imagen" name="imagen" class="form-control" accept="image/jpeg,image/png,image/webp">
                        </div>

                        <div class="col-md-4 d-flex align-items-center pt-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="editar_publicado" name="publicado" value="1">
                                <label class="form-check-label fw-bold text-secondary" for="editar_publicado">Visible al público</label>
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
    <script src="{{ asset('js/admin/novedades.js') }}"></script>
@endsection