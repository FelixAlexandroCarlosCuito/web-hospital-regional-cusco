<ul class="nav nav-pills bg-white p-2 rounded-3 shadow-sm mb-4 gap-2">
    <li class="nav-item">
        <a class="nav-link fw-bold {{ request()->routeIs('admin.panel') ? 'active' : 'text-secondary' }}"
           href="{{ route('admin.panel') }}">
            Resumen y Métricas
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link fw-bold {{ request()->routeIs('admin.especialidades.*') ? 'active' : 'text-secondary' }}"
           href="{{ route('admin.especialidades.index') }}">
            Especialidades
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link fw-bold {{ request()->routeIs('admin.personal.*') ? 'active' : 'text-secondary' }}"
        href="{{ route('admin.personal.index') }}">
            Médicos y Personal
        </a>
    <li class="nav-item">
        <a class="nav-link fw-bold {{ request()->routeIs('admin.novedades.*') ? 'active' : 'text-secondary' }}"
        href="{{ route('admin.novedades.index') }}">
            Novedades
        </a>
    </li>
</ul>