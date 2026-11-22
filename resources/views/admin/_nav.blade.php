{{-- Navegación interna compartida por los módulos administrativos --}}
<nav aria-label="Módulos de administración">
    <ul class="nav nav-admin-mate d-inline-flex flex-wrap gap-1 mb-0">
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.panel') ? 'active' : '' }}"
               href="{{ route('admin.panel') }}"
               @if(request()->routeIs('admin.panel')) aria-current="page" @endif>
                <i class="bi bi-bar-chart-line me-1"></i>Resumen y Métricas
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.especialidades.*') ? 'active' : '' }}"
               href="{{ route('admin.especialidades.index') }}"
               @if(request()->routeIs('admin.especialidades.*')) aria-current="page" @endif>
                <i class="bi bi-grid me-1"></i>Especialidades
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.personal.*') ? 'active' : '' }}"
               href="{{ route('admin.personal.index') }}"
               @if(request()->routeIs('admin.personal.*')) aria-current="page" @endif>
                <i class="bi bi-people me-1"></i>Médicos y Personal
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.novedades.*') ? 'active' : '' }}"
               href="{{ route('admin.novedades.index') }}"
               @if(request()->routeIs('admin.novedades.*')) aria-current="page" @endif>
                <i class="bi bi-newspaper me-1"></i>Novedades
            </a>
        </li>
    </ul>
</nav>