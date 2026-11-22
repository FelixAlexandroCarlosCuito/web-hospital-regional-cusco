@extends('layouts.main')

@section('titulo', 'Gestión de Novedades')
@section('clase_main', 'p-0')

@section('contenido')
    @php
        $totalNovedades = $novedades->count();
        $publicadasCount = $novedades->where('publicado', true)->count();
        $borradoresCount = $totalNovedades - $publicadasCount;
        $conImagenCount = $novedades->filter(fn ($n) => !empty($n->imagen))->count();
        $publicadoCrearPorDefecto = old('_token') ? (bool) old('publicado') : true;
    @endphp

    <section class="cabecera-interna-mate seccion-pizarra py-4">
        <div class="container py-2">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                <div>
                    <span class="sobretitulo-mate d-block mb-1">Comunicación institucional</span>
                    <h1 class="h3 fw-bold text-hrc-primario mb-1">Novedades y Comunicados</h1>
                    <p class="text-muted small mb-0">
                        Publicación de noticias, campañas de salud y avisos oficiales del hospital.
                    </p>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('novedades.index') }}"
                       target="_blank"
                       rel="noopener"
                       class="btn btn-outline-hrc-primario rounded-pill px-4">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Portal Público
                    </a>
                    <button type="button"
                            class="btn btn-hrc-acento rounded-pill px-4"
                            data-bs-toggle="modal"
                            data-bs-target="#modalCrearNovedad">
                        <i class="bi bi-plus-lg me-1"></i> Nueva Novedad
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

            {{-- Indicadores de publicaciones y borradores --}}
            <div class="panel-card-elevada p-4 mb-4">
                <div class="row g-3">
                    <div class="col-6 col-lg-3">
                        <div class="kpi-bloque-interno p-3 h-100">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div class="kpi-icono-cuadro kpi-icono-azul">
                                    <i class="bi bi-newspaper"></i>
                                </div>
                                <div class="small fw-bold text-hrc-primario lh-sm">Total Registradas</div>
                            </div>
                            <div class="h3 fw-bold text-hrc-primario mb-0">{{ $totalNovedades }}</div>
                        </div>
                    </div>

                    <div class="col-6 col-lg-3">
                        <div class="kpi-bloque-interno p-3 h-100">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div class="kpi-icono-cuadro kpi-icono-salvia">
                                    <i class="bi bi-globe2"></i>
                                </div>
                                <div class="small fw-bold text-hrc-primario lh-sm">Publicadas</div>
                            </div>
                            <div class="h3 fw-bold text-hrc-primario mb-0">{{ $publicadasCount }}</div>
                        </div>
                    </div>

                    <div class="col-6 col-lg-3">
                        <div class="kpi-bloque-interno p-3 h-100">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div class="kpi-icono-cuadro kpi-icono-ocre">
                                    <i class="bi bi-file-earmark-text"></i>
                                </div>
                                <div class="small fw-bold text-hrc-primario lh-sm">Borradores</div>
                            </div>
                            <div class="h3 fw-bold text-hrc-primario mb-0">{{ $borradoresCount }}</div>
                        </div>
                    </div>

                    <div class="col-6 col-lg-3">
                        <div class="kpi-bloque-interno p-3 h-100">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div class="kpi-icono-cuadro kpi-icono-terracota">
                                    <i class="bi bi-image"></i>
                                </div>
                                <div class="small fw-bold text-hrc-primario lh-sm">Con Portada</div>
                            </div>
                            <div class="d-flex align-items-baseline gap-2">
                                <span class="h3 fw-bold text-hrc-primario mb-0">{{ $conImagenCount }}</span>
                                <span class="texto-xs text-muted">de {{ $totalNovedades }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabla editorial con búsqueda y filtro por estado --}}
            <div class="panel-card-elevada overflow-hidden">
                <div class="px-4 pt-4 pb-3 border-bottom d-flex flex-column gap-3">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div>
                            <h2 class="h6 fw-bold titulo-panel-acento mb-0">Listado de Publicaciones</h2>
                            <span class="texto-xs text-muted">
                                Las novedades publicadas aparecen en la portada y en la sección Novedades
                            </span>
                        </div>

                        @if ($totalNovedades > 0)
                            <div class="buscador-tabla-panel d-flex align-items-center">
                                <i class="bi bi-search text-muted ms-3 small"></i>
                                <input type="text"
                                       id="buscador-admin-novedades"
                                       class="form-control form-control-sm border-0 bg-transparent shadow-none px-2"
                                       placeholder="Buscar título o categoría..."
                                       aria-label="Buscar novedad por título o categoría">
                            </div>
                        @endif
                    </div>

                    @if ($totalNovedades > 0)
                        <div class="d-flex flex-wrap gap-2" role="group" aria-label="Filtrar novedades por estado">
                            <button type="button" class="tab-estado-panel activo" data-estado="todas">
                                Todas ({{ $totalNovedades }})
                            </button>
                            <button type="button" class="tab-estado-panel" data-estado="publicado">
                                Publicadas ({{ $publicadasCount }})
                            </button>
                            <button type="button" class="tab-estado-panel" data-estado="borrador">
                                Borradores ({{ $borradoresCount }})
                            </button>
                        </div>
                    @endif
                </div>

                <div class="table-responsive">
                    <table class="table tabla-panel-suave align-middle mb-0 small">
                        <thead>
                            <tr>
                                <th class="ps-4">Portada</th>
                                <th>Título y Resumen</th>
                                <th>Categoría</th>
                                <th>Fecha</th>
                                <th class="text-center">Estado</th>
                                <th class="text-end pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($novedades as $novedad)
                                @php
                                    $indiceBusqueda = trim("{$novedad->titulo} {$novedad->resumen} {$novedad->categoria}");
                                @endphp
                                <tr class="fila-admin-novedad {{ $novedad->publicado ? '' : 'fila-cita-inactiva' }}"
                                    data-estado="{{ $novedad->publicado ? 'publicado' : 'borrador' }}"
                                    data-busqueda="{{ $indiceBusqueda }}">
                                    <td class="ps-4">
                                        @if ($novedad->imagen)
                                            <img src="{{ asset('storage/' . $novedad->imagen) }}"
                                                 alt="Portada de {{ $novedad->titulo }}"
                                                 class="thumb-novedad-admin">
                                        @else
                                            <div class="thumb-novedad-vacia d-flex align-items-center justify-content-center text-muted">
                                                <i class="bi bi-image"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold {{ $novedad->publicado ? 'text-hrc-primario' : 'text-muted' }}">
                                            {{ $novedad->titulo }}
                                        </div>
                                        <div class="texto-xs text-muted">
                                            {{ \Illuminate\Support\Str::limit($novedad->resumen, 95) }}
                                        </div>
                                    </td>
                                    <td class="text-nowrap">
                                        <span class="badge-categoria-mate">{{ $novedad->categoria }}</span>
                                    </td>
                                    <td class="text-nowrap">
                                        @if ($novedad->fecha_publicacion)
                                            <span class="text-dark fw-semibold">
                                                {{ $novedad->fecha_publicacion->format('d/m/Y') }}
                                            </span>
                                        @else
                                            <span class="text-muted">Sin publicar</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($novedad->publicado)
                                            <span class="ticket-pill-estado badge-agenda-atendido py-1 px-2 texto-xs">Publicado</span>
                                        @else
                                            <span class="etiqueta-sin-cupos-mate py-1 px-2 texto-xs">Borrador</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4 text-nowrap">
                                        <div class="d-inline-flex align-items-center gap-2">
                                            <button type="button"
                                                    class="btn btn-icono-panel btn-sm btn-editar-novedad"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalEditarNovedad"
                                                    data-action="{{ route('admin.novedades.update', $novedad) }}"
                                                    data-titulo="{{ $novedad->titulo }}"
                                                    data-categoria="{{ $novedad->categoria }}"
                                                    data-resumen="{{ $novedad->resumen }}"
                                                    data-contenido="{{ $novedad->contenido }}"
                                                    data-publicado="{{ $novedad->publicado ? '1' : '0' }}"
                                                    data-imagen-url="{{ $novedad->imagen ? asset('storage/' . $novedad->imagen) : '' }}">
                                                <i class="bi bi-pencil-square me-1"></i>Editar
                                            </button>

                                            <form action="{{ route('admin.novedades.toggle', $novedad) }}" method="POST" class="m-0">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="btn btn-sm rounded-pill px-3 {{ $novedad->publicado ? 'btn-outline-hrc-primario' : 'btn-activar-mate' }}">
                                                    {{ $novedad->publicado ? 'Ocultar' : 'Publicar' }}
                                                </button>
                                            </form>

                                            <button type="button"
                                                    class="btn btn-cancelar-mate btn-sm rounded-pill px-3 btn-abrir-eliminar-novedad"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalEliminarNovedad"
                                                    data-action="{{ route('admin.novedades.destroy', $novedad) }}"
                                                    data-titulo="{{ $novedad->titulo }}">
                                                Eliminar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        Aún no se han registrado novedades ni comunicados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div id="sin-resultados-novedades-admin" class="text-center py-4 text-muted small d-none">
                    No se encontraron publicaciones que coincidan con el filtro aplicado.
                </div>
            </div>

        </div>
    </section>

    {{-- Modal de registro de nueva publicación --}}
    <div class="modal fade" id="modalCrearNovedad" tabindex="-1" aria-labelledby="modalCrearNovedadLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow modal-mate">
                <form id="formCrearNovedad" action="{{ route('admin.novedades.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                        <h5 class="modal-title fw-bold text-hrc-primario" id="modalCrearNovedadLabel">Nueva Novedad</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body px-4 py-3">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="crear_titulo" class="form-label small fw-bold text-hrc-primario">Título</label>
                                <input type="text"
                                       id="crear_titulo"
                                       name="titulo"
                                       value="{{ old('titulo') }}"
                                       class="form-control input-mate"
                                       maxlength="180"
                                       required
                                       placeholder="Ej: Campaña gratuita de vacunación contra la influenza">
                            </div>
                            <div class="col-md-4">
                                <label for="crear_categoria" class="form-label small fw-bold text-hrc-primario">Categoría</label>
                                <select id="crear_categoria" name="categoria" class="form-select input-mate" required>
                                    @foreach ($categorias as $cat)
                                        <option value="{{ $cat }}" {{ old('categoria') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label for="crear_resumen" class="form-label small fw-bold text-hrc-primario">Resumen breve</label>
                                <textarea id="crear_resumen"
                                          name="resumen"
                                          class="form-control input-mate"
                                          rows="2"
                                          maxlength="300"
                                          required
                                          placeholder="Texto corto para la tarjeta del listado (máx. 300 caracteres)">{{ old('resumen') }}</textarea>
                            </div>

                            <div class="col-12">
                                <label for="crear_contenido" class="form-label small fw-bold text-hrc-primario">Contenido completo</label>
                                <textarea id="crear_contenido"
                                          name="contenido"
                                          class="form-control input-mate"
                                          rows="6"
                                          required
                                          placeholder="Detalle completo de la noticia, fechas, requisitos o lugar de atención">{{ old('contenido') }}</textarea>
                            </div>

                            <div class="col-md-8">
                                <label for="crear_imagen" class="form-label small fw-bold text-hrc-primario">
                                    Imagen de portada <span class="fw-normal text-muted">(Opcional)</span>
                                </label>
                                <input type="file"
                                       id="crear_imagen"
                                       name="imagen"
                                       class="form-control input-mate"
                                       accept="image/jpeg,image/png,image/webp">
                                <div class="form-text texto-xs">Formatos JPG, PNG o WEBP. Máximo 2 MB.</div>
                            </div>

                            <div class="col-md-4 d-flex align-items-center pt-md-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           id="crear_publicado"
                                           name="publicado"
                                           value="1"
                                           {{ $publicadoCrearPorDefecto ? 'checked' : '' }}>
                                    <label class="form-check-label small fw-bold text-hrc-primario" for="crear_publicado">
                                        Publicar de inmediato
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 px-4 pb-4 pt-2">
                        <button type="button" class="btn btn-outline-hrc-primario rounded-pill px-4" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-hrc-acento rounded-pill px-4">
                            Guardar Novedad
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal de edición de publicación existente --}}
    <div class="modal fade" id="modalEditarNovedad" tabindex="-1" aria-labelledby="modalEditarNovedadLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow modal-mate">
                <form id="formEditarNovedad" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                        <h5 class="modal-title fw-bold text-hrc-primario" id="modalEditarNovedadLabel">Editar Novedad</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body px-4 py-3">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="editar_titulo" class="form-label small fw-bold text-hrc-primario">Título</label>
                                <input type="text" id="editar_titulo" name="titulo" class="form-control input-mate" maxlength="180" required>
                            </div>
                            <div class="col-md-4">
                                <label for="editar_categoria" class="form-label small fw-bold text-hrc-primario">Categoría</label>
                                <select id="editar_categoria" name="categoria" class="form-select input-mate" required>
                                    @foreach ($categorias as $cat)
                                        <option value="{{ $cat }}">{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label for="editar_resumen" class="form-label small fw-bold text-hrc-primario">Resumen breve</label>
                                <textarea id="editar_resumen" name="resumen" class="form-control input-mate" rows="2" maxlength="300" required></textarea>
                            </div>

                            <div class="col-12">
                                <label for="editar_contenido" class="form-label small fw-bold text-hrc-primario">Contenido completo</label>
                                <textarea id="editar_contenido" name="contenido" class="form-control input-mate" rows="6" required></textarea>
                            </div>

                            <div class="col-12" id="bloque-imagen-actual" style="display: none;">
                                <div class="resumen-reserva-mate p-3 d-flex align-items-center gap-3">
                                    <img id="preview-imagen-actual" src="" alt="Portada actual" class="thumb-novedad-admin">
                                    <div class="form-check mb-0">
                                        <input class="form-check-input" type="checkbox" id="editar_eliminar_imagen" name="eliminar_imagen" value="1">
                                        <label class="form-check-label small fw-bold text-hrc-acento" for="editar_eliminar_imagen">
                                            Quitar imagen actual
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-8">
                                <label for="editar_imagen" class="form-label small fw-bold text-hrc-primario">
                                    Reemplazar imagen <span class="fw-normal text-muted">(Opcional)</span>
                                </label>
                                <input type="file"
                                       id="editar_imagen"
                                       name="imagen"
                                       class="form-control input-mate"
                                       accept="image/jpeg,image/png,image/webp">
                            </div>

                            <div class="col-md-4 d-flex align-items-center pt-md-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="editar_publicado" name="publicado" value="1">
                                    <label class="form-check-label small fw-bold text-hrc-primario" for="editar_publicado">
                                        Visible al público
                                    </label>
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

    {{-- Modal de confirmación para eliminación definitiva --}}
    <div class="modal fade" id="modalEliminarNovedad" tabindex="-1" aria-labelledby="modalEliminarNovedadLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow modal-mate">
                <form id="formEliminarNovedad" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                        <h5 class="modal-title fw-bold text-hrc-primario" id="modalEliminarNovedadLabel">
                            ¿Eliminar publicación definitivamente?
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body px-4 py-3 small text-muted">
                        <p class="mb-3">
                            Esta acción eliminará el comunicado y su imagen asociada de forma permanente. Si solo deseas retirarlo del portal, usa el botón <strong>Ocultar</strong>.
                        </p>
                        <div class="resumen-reserva-mate p-3">
                            <span class="texto-xs text-muted d-block mb-1">Publicación seleccionada</span>
                            <strong id="modal-eliminar-novedad-titulo" class="text-hrc-primario">—</strong>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 px-4 pb-4 pt-2">
                        <button type="button" class="btn btn-outline-hrc-primario rounded-pill px-4" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button type="submit" id="btn-confirmar-eliminar-novedad" class="btn btn-hrc-acento rounded-pill px-4">
                            Sí, eliminar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('js/admin/novedades.js') }}"></script>
@endsection