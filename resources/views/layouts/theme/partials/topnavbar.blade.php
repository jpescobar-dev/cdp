<div class="topbar-nav header navbar" role="banner">
    <nav id="topbar">
        <ul class="navbar-nav theme-brand flex-row text-center">
            <li class="nav-item theme-logo">
                <a href="{{ route('dashboard') }}">
                    <img src="{{ asset('assets/img/90x90.jpg') }}" class="navbar-logo" alt="logo">
                </a>
            </li>
            <li class="nav-item theme-text">
                <a href="{{ route('dashboard') }}" class="nav-link">CAPJ</a>
            </li>
        </ul>

        <ul class="list-unstyled menu-categories" id="topAccordion">

            {{-- INICIO --}}
            @can('ver dashboard')
                <li class="menu single-menu {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}"
                       aria-expanded="{{ request()->routeIs('dashboard') ? 'true' : 'false' }}"
                       class="dropdown-toggle">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 width="24"
                                 height="24"
                                 viewBox="0 0 24 24"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="2"
                                 stroke-linecap="round"
                                 stroke-linejoin="round"
                                 class="feather feather-home">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                            <span>Inicio</span>
                        </div>
                    </a>
                </li>
            @endcan

            {{-- SISTEMA --}}
            @php
                $puedeVerSistema = auth()->check() && (
                    auth()->user()->can('ver usuarios')
                    || auth()->user()->can('ver revisiones contractuales')
                );

                $menuSistemaActivo =
                    request()->routeIs('usuarios.*') ||
                    request()->routeIs('contractual.*');
            @endphp

            @if($puedeVerSistema)
                <li class="menu single-menu {{ $menuSistemaActivo ? 'active' : '' }}">
                    <a href="#menu-sistema"
                       data-bs-toggle="collapse"
                       aria-expanded="{{ $menuSistemaActivo ? 'true' : 'false' }}"
                       class="dropdown-toggle autodroprown">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 width="24"
                                 height="24"
                                 viewBox="0 0 24 24"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="2"
                                 stroke-linecap="round"
                                 stroke-linejoin="round"
                                 class="feather feather-settings">
                                <circle cx="12" cy="12" r="3"></circle>
                                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9c0 .66.39 1.26 1 1.51.16.07.33.1.51.1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                            </svg>
                            <span>Sistema</span>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg"
                             width="24"
                             height="24"
                             viewBox="0 0 24 24"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="2"
                             stroke-linecap="round"
                             stroke-linejoin="round"
                             class="feather feather-chevron-down">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </a>

                    <ul class="collapse submenu list-unstyled {{ $menuSistemaActivo ? 'show' : '' }}"
                        id="menu-sistema"
                        data-parent="#topAccordion">

                        @can('ver usuarios')
                            <li class="{{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                                <a href="{{ route('usuarios.index') }}">Usuarios</a>
                            </li>
                        @endcan

                        @can('ver revisiones contractuales')
                            <li class="{{ request()->routeIs('contractual.revisiones.*') ? 'active' : '' }}">
                                <a href="{{ route('contractual.revisiones.index') }}">Revisiones Contractuales</a>
                            </li>
                        @endcan

                    </ul>
                </li>
            @endif

        </ul>
    </nav>
</div>