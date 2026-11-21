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
        <a class="nav-link text-secondary disabled" href="#" tabindex="-1" aria-disabled="true">
            Médicos y Personal (Próximo)
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-secondary disabled" href="#" tabindex="-1" aria-disabled="true">
            Novedades (Próximo)
        </a>
    </li>
</ul>