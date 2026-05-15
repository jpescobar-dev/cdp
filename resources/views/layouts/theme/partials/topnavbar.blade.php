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

            @can('ver dashboard')
                <li class="menu single-menu {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}" class="dropdown-toggle">
                        <div>
                            <i data-feather="home"></i>
                            <span>Inicio</span>
                        </div>
                    </a>
                </li>
            @endcan

            @php
                $menuMesaAyudaActivo = request()->routeIs('mesa-ayuda.*');
            @endphp

            <li class="menu single-menu {{ $menuMesaAyudaActivo ? 'active' : '' }}">
                <a href="#menu-mesa-ayuda"
                   data-toggle="collapse"
                   aria-expanded="{{ $menuMesaAyudaActivo ? 'true' : 'false' }}"
                   class="dropdown-toggle">
                    <div>
                        <i data-feather="inbox"></i>
                        <span>Mesa de Ayuda</span>
                    </div>
                    <i data-feather="chevron-down"></i>
                </a>

                <ul class="collapse submenu list-unstyled {{ $menuMesaAyudaActivo ? 'show' : '' }}"
                    id="menu-mesa-ayuda"
                    data-parent="#topAccordion">

                    @if(Route::has('mesa-ayuda.extracciones.index'))
                        <li class="{{ request()->routeIs('mesa-ayuda.extracciones.*') ? 'active' : '' }}">
                            <a href="{{ route('mesa-ayuda.extracciones.index') }}">
                                Conexión / Extracciones
                            </a>
                        </li>
                    @endif

                    @if(Route::has('mesa-ayuda.requerimientos.index'))
                        <li class="{{ request()->routeIs('mesa-ayuda.requerimientos.*') ? 'active' : '' }}">
                            <a href="{{ route('mesa-ayuda.requerimientos.index') }}">
                                Requerimientos
                            </a>
                        </li>
                    @endif

                    @if(Route::has('mesa-ayuda.cdp-borradores.index'))
                        <li class="{{ request()->routeIs('mesa-ayuda.cdp-borradores.*') ? 'active' : '' }}">
                            <a href="{{ route('mesa-ayuda.cdp-borradores.index') }}">
                                Borradores CDP
                            </a>
                        </li>
                    @endif

                </ul>
            </li>

            @php
                $menuPresupuestoActivo = request()->routeIs('presupuesto.expedientes.*')
                    || request()->routeIs('cdp.solicitudes.*');
            @endphp

            <li class="menu single-menu {{ $menuPresupuestoActivo ? 'active' : '' }}">
                <a href="#menu-presupuesto"
                   data-toggle="collapse"
                   aria-expanded="{{ $menuPresupuestoActivo ? 'true' : 'false' }}"
                   class="dropdown-toggle">
                    <div>
                        <i data-feather="file-text"></i>
                        <span>Presupuesto</span>
                    </div>
                    <i data-feather="chevron-down"></i>
                </a>

                <ul class="collapse submenu list-unstyled {{ $menuPresupuestoActivo ? 'show' : '' }}"
                    id="menu-presupuesto"
                    data-parent="#topAccordion">

                    @if(Route::has('cdp.solicitudes.index'))
                        <li class="{{ request()->routeIs('cdp.solicitudes.*') ? 'active' : '' }}">
                            <a href="{{ route('cdp.solicitudes.index') }}">Solicitudes CDP</a>
                        </li>
                    @endif

                    @if(Route::has('presupuesto.expedientes.index'))
                        <li class="{{ request()->routeIs('presupuesto.expedientes.*') ? 'active' : '' }}">
                            <a href="{{ route('presupuesto.expedientes.index') }}">Expedientes</a>
                        </li>
                    @endif

                </ul>
            </li>

            @php
                $menuAgentesActivo = request()->routeIs('agentes.*');
            @endphp

            @if(Route::has('agentes.ejecuciones.index') || Route::has('agentes.monitor'))
                <li class="menu single-menu {{ $menuAgentesActivo ? 'active' : '' }}">
                    <a href="#menu-agentes"
                       data-toggle="collapse"
                       aria-expanded="{{ $menuAgentesActivo ? 'true' : 'false' }}"
                       class="dropdown-toggle">
                        <div>
                            <i data-feather="cpu"></i>
                            <span>Agentes</span>
                        </div>
                        <i data-feather="chevron-down"></i>
                    </a>

                    <ul class="collapse submenu list-unstyled {{ $menuAgentesActivo ? 'show' : '' }}"
                        id="menu-agentes"
                        data-parent="#topAccordion">
                        @if(Route::has('agentes.ejecuciones.index'))
                            <li class="{{ request()->routeIs('agentes.ejecuciones.*') ? 'active' : '' }}">
                                <a href="{{ route('agentes.ejecuciones.index') }}">Ejecuciones</a>
                            </li>
                        @endif
                        @if(Route::has('agentes.monitor'))
                            <li class="{{ request()->routeIs('agentes.monitor') ? 'active' : '' }}">
                                <a href="{{ route('agentes.monitor') }}">Monitor</a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            @php
                $puedeVerSistema = auth()->check() && auth()->user()->can('ver usuarios');
                $menuSistemaActivo = request()->routeIs('usuarios.*');
            @endphp

            @if($puedeVerSistema)
                <li class="menu single-menu {{ $menuSistemaActivo ? 'active' : '' }}">
                    <a href="#menu-sistema"
                       data-toggle="collapse"
                       aria-expanded="{{ $menuSistemaActivo ? 'true' : 'false' }}"
                       class="dropdown-toggle">
                        <div>
                            <i data-feather="settings"></i>
                            <span>Sistema</span>
                        </div>
                        <i data-feather="chevron-down"></i>
                    </a>
                    <ul class="collapse submenu list-unstyled {{ $menuSistemaActivo ? 'show' : '' }}"
                        id="menu-sistema"
                        data-parent="#topAccordion">
                        @can('ver usuarios')
                            @if(Route::has('usuarios.index'))
                                <li class="{{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                                    <a href="{{ route('usuarios.index') }}">Usuarios</a>
                                </li>
                            @endif
                        @endcan
                    </ul>
                </li>
            @endif
        </ul>
    </nav>
</div>
