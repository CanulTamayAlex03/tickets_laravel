<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Tickets</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/layout.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.css') }}">
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet">
     @stack('scripts')
</head>

<body>
    <!-- Navbar Superior -->
    <nav class="top-navbar navbar navbar-expand">
        <div class="container-fluid">
            <button class="btn text-light" id="sidebarToggle">
                <i class="bi bi-list"></i>
                Departamento de Informática
            </button>

            <div class="navbar-nav ms-auto">
                @auth
                <span class="nav-link text-light">
                    <i class="bi bi-person-fill me-1"></i>
                    Hola, {{ auth()->user()->email }}
                    <small class="d-block">
                        @foreach(auth()->user()->getRoleNames() as $role)
                        <span class="badge bg-secondary">{{ $role }}</span>
                        @endforeach
                    </small>
                </span>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar text-white" id="sidebar">
        <div class="sidebar-header">
            <img src="{{ asset('images/logodif.jpg') }}" alt="Menú" class="sidebar-logo">
        </div>
        <div class="p-3 d-flex flex-column" style="height: calc(100% - 100px);">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link text-white @if(Request::is('/')) active @endif" href="{{ url('/') }}">
                        <i class="bi bi-house-door me-2"></i> Inicio
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white @if(Request::is('solicitudes*')) active @endif" href="{{ route('solicitudes.solicitudes') }}">
                        <i class="bi bi-ticket-detailed me-2"></i> Mis solicitudes
                    </a>
                </li>
                
                @guest
                <li class="nav-item">
                    <a class="nav-link text-white @if(Request::is('admin*') || Request::is('administrador*')) active @endif" href="{{ route('login') }}">
                        <i class="bi bi-shield-lock me-2"></i> Administrador
                    </a>
                </li>
                @endguest

                @auth
                <!-- SECCIÓN ADMINISTRADOR - Verificar por PERMISO en lugar de ROL -->
                @can('acceso administrador')
                <p></p>
                <h6>ADMINISTRADOR:</h6>
                <div class="my-2" style="border-top: 2px solid rgba(255, 255, 255, 0.5);"></div>

                    @can('ver tickets')
                    <li class="nav-item">
                        <a class="nav-link text-white @if(Request::is('admin/admin_solicitudes')) active @endif" href="{{ route('admin.admin_solicitudes') }}">
                            <i class="bi bi-ticket-detailed me-2"></i> Administrar Solicitudes
                        </a>
                    </li>
                    @endcan

                    @can('ver usuarios')
                    <li class="nav-item">
                        <a class="nav-link text-white @if(Request::is('admin/usuarios')) active @endif" href="{{ route('admin.usuarios') }}">
                            <i class="bi bi-people me-2"></i> Usuarios
                        </a>
                    </li>
                    @endcan

                    @can('ver tipos usuario')
                    <li class="nav-item">
                        <a class="nav-link text-white @if(Request::is('admin/usuarios_tipo')) active @endif" href="{{ route('admin.usuarios_tipo') }}">
                            <i class="bi bi-person-gear me-2"></i>Roles y Permisos
                        </a>
                    </li>
                    @endcan

                    @can('ver personal soporte')
                    <li class="nav-item">
                        <a class="nav-link text-white @if(Request::is('admin/soporte')) active @endif" href="{{ route('admin.soporte') }}">
                            <i class="bi bi-headset me-2"></i> Personal de soporte
                        </a>
                    </li>
                    @endcan

                    @can('ver reportes')
                    <li class="nav-item">
                        <a class="nav-link text-white @if(Request::is('admin/reportes')) active @endif" href="{{ route('admin.reportes') }}">
                            <i class="bi bi-clipboard2-data me-2"></i> Reportes
                        </a>
                    </li>
                    @endcan

                    <!-- Sección exclusiva para superadmin 
                    @role('superadmin')
                    <li class="nav-item">
                        <a class="nav-link text-white @if(Request::is('admin/gestion-permisos')) active @endif" href="{{ route('admin.permisos.manager') }}">
                            <i class="bi bi-check2-square me-2"></i> Gestión de Permisos
                        </a>
                    </li>
                    @endrole -->
                @endcan
                @endauth
            </ul>

            <!-- Cerrar sesión al final -->
            @auth
            <div class="mt-auto">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a class="nav-link text-white" href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                                <i class="bi bi-box-arrow-right me-2"></i> Cerrar sesión
                            </a>
                        </form>
                    </li>
                </ul>
            </div>
            @endauth
        </div>
    </div>

    <!-- Contenido Principal -->
    <div class="main-content" id="mainContent">
        <div style="height: 56px;"></div>
        @yield('content')
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/es.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const topNavbar = document.querySelector('.top-navbar');
            const sidebarToggle = document.getElementById('sidebarToggle');

            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('sidebar-collapsed');
                mainContent.classList.toggle('content-collapsed');
                topNavbar.classList.toggle('navbar-collapsed');
                const isCollapsed = sidebar.classList.contains('sidebar-collapsed');
                localStorage.setItem('sidebarCollapsed', isCollapsed);
            });

            if (localStorage.getItem('sidebarCollapsed') === 'true') {
                sidebar.classList.add('sidebar-collapsed');
                mainContent.classList.add('content-collapsed');
                topNavbar.classList.add('navbar-collapsed');
            }

            if (window.innerWidth <= 768) {
                document.querySelector('.main-content').style.paddingTop = '76px';
            }
        });
    </script>
    @yield('scripts')
</body>

</html>