<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistema DIF')</title>

    <!-- CSS compilado con Mix -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.css') }}">
    <link href="{{ asset('css/layout.css') }}" rel="stylesheet">

    <link rel="shortcut icon" href="{{ asset('images/buena-persona.png') }}" type="image/png">
    <link rel="icon" href="{{ asset('images/buena-persona.png') }}" type="image/png">

</head>

<body>
    @auth
    <!-- ========== Sidebar ========== -->
    <div class="sidebar bg-dark text-white" id="sidebar">
        <div class="sidebar-header">
            <img src="{{ asset('images/logodif.jpg') }}" alt="Logo DIF" class="sidebar-logo">
            <div class="sidebar-header-content">
                <div class="d-flex align-items-center"></div>
                <button class="btn btn-link text-white p-0 d-md-none" id="mobileSidebarClose">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </div>
        <ul class="nav flex-column px-2 pt-3">

            {{-- ================== BENEFICIARIOS ================== --}}
            <div class="sidebar-section-title">Beneficiarios</div>

            @can('ver beneficiarios')
            <li class="nav-item mb-2">
                <a class="nav-link text-white {{ request()->routeIs('beneficiarios') ? 'active bg-secondary rounded' : '' }}"
                    href="{{ route('beneficiarios') }}">
                    <i class="bi bi-person-lines-fill me-2"></i>
                    <span class="nav-link-text">Padrón</span>
                </a>
            </li>
            @endcan

            @can('importar beneficiarios')
            <li class="nav-item mb-2">
                <a class="nav-link text-white" href="{{ route('administrador.importar_beneficiarios') }}">
                    <i class="bi bi-file-earmark-spreadsheet-fill me-2"></i>
                    <span class="nav-link-text">Importar</span>
                </a>
            </li>
            @endcan

            {{-- ================== CATÁLOGOS ================== --}}
            @can('acceder panel administracion')
            <div class="sidebar-section-title">Catálogos</div>

            @can('ver usuarios')
            <li class="nav-item mb-2">
                <a class="nav-link text-white {{ request()->is('administrador/usuarios*') ? 'active bg-secondary rounded' : '' }}"
                    href="{{ route('usuarios.index') }}">
                    <i class="bi bi-person-gear me-2"></i>
                    <span class="nav-link-text">Usuarios</span>
                </a>
            </li>
            @endcan

            @can('gestionar roles')
            <li class="nav-item mb-2">
                <a class="nav-link text-white {{ request()->is('roles*') ? 'active bg-secondary rounded' : '' }}" href="{{ route('roles.index') }}">
                    <i class="bi bi-shield-lock me-2"></i>
                    <span class="nav-link-text">Roles y Permisos</span>
                </a>
            </li>
            @endcan

            <!--
            @can('ver areas')
            <li class="nav-item mb-2">
                <a class="nav-link text-white" href="{{ route('administrador.areas') }}">
                    <i class="bi bi-diagram-3-fill me-2"></i>
                    <span class="nav-link-text">Áreas</span>
                </a>
            </li>
            @endcan -->
            @endcan
        </ul>
        <div class="sidebar-footer mt-auto p-2">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-light w-100 d-flex align-items-center justify-content-center">
                    <i class="bi bi-box-arrow-right me-2"></i>
                    Cerrar sesión
                </button>
            </form>
        </div>
    </div>

    <!-- ========== Navbar ========== -->
    <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top shadow-sm" id="navbar">
        <div class="container-fluid">
            <button class="btn btn-dark" id="sidebarToggle">
                <i class="bi bi-list"></i> Padrón de Beneficiarios
            </button>
            <div class="navbar-brand ms-3"></div>

            <!-- Menú derecho -->
            <div class="d-flex ms-auto align-items-center">
                <span class="text-white me-3">
                    <i class="bi bi-person-circle me-1"></i>
                    {{ Auth::user()->email }}
                    <span class="badge bg-light text-dark ms-1">
                        {{ Auth::user()->getRoleNames()->implode(', ') }}
                    </span>
                </span>
            </div>
        </div>
    </nav>
    @endauth

    <!-- ========== Contenido Principal ========== -->
    <main class="main-content @auth py-4 @endauth" id="mainContent">
        @yield('content')
    </main>

    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }

        .sidebar {
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        .sidebar.collapsed .sidebar-footer button {
            font-size: 0;
            justify-content: center;
        }

        .sidebar.collapsed .sidebar-footer i {
            margin: 0;
        }

        .sidebar-footer {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }
    </style>

    <!-- ========== JavaScripts ========== -->
    <!-- jQuery -->
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>

    <!-- Bootstrap (bundle incluye Popper) -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

    <!-- FINALMENTE tu app.js -->
    <script src="{{ mix('js/app.js') }}"></script>

    @auth
    <script>
        // Inicializar todos los componentes de Bootstrap manualmente
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Inicializar popovers
            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl);
            });

            // Inicializar dropdowns
            var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
            var dropdownList = dropdownElementList.map(function(dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl);
            });

            // Inicializar modals
            var modalElementList = [].slice.call(document.querySelectorAll('.modal'));
            var modalList = modalElementList.map(function(modalEl) {
                return new bootstrap.Modal(modalEl);
            });

            // Tu código existente del sidebar
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const mobileSidebarClose = document.getElementById('mobileSidebarClose');
            const navbar = document.getElementById('navbar');
            const mainContent = document.getElementById('mainContent');
            let isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';

            if (window.innerWidth >= 768) {
                applySidebarState(isCollapsed);
            }

            function applySidebarState(collapsed) {
                if (window.innerWidth >= 768) {
                    if (collapsed) {
                        sidebar.classList.add('collapsed');
                        navbar.classList.add('navbar-collapsed');
                        mainContent.classList.add('main-content-collapsed');
                    } else {
                        sidebar.classList.remove('collapsed');
                        navbar.classList.remove('navbar-collapsed');
                        mainContent.classList.remove('main-content-collapsed');
                    }
                }
            }

            function toggleSidebar() {
                isCollapsed = !isCollapsed;
                localStorage.setItem('sidebarCollapsed', isCollapsed);

                if (window.innerWidth >= 768) {
                    applySidebarState(isCollapsed);
                } else {
                    sidebar.classList.toggle('active');
                }
            }

            function closeMobileSidebar() {
                sidebar.classList.remove('active');
            }

            sidebarToggle.addEventListener('click', toggleSidebar);
            mobileSidebarClose.addEventListener('click', closeMobileSidebar);

            window.addEventListener('resize', function() {
                if (window.innerWidth >= 768) {
                    sidebar.classList.remove('active');
                    applySidebarState(isCollapsed);
                } else {
                    sidebar.classList.remove('collapsed');
                    navbar.classList.remove('navbar-collapsed');
                    mainContent.classList.remove('main-content-collapsed');
                }
            });
        });
    </script>
    @endauth

    @stack('scripts')
    @yield('scripts')

</body>

</html>