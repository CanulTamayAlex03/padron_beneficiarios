<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema DIF')</title>
    <!-- CSS compilado con Mix -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.css') }}">
    <link href="{{ asset('css/layout.css') }}" rel="stylesheet">

</head>

<body>
    @auth
    <!-- ========== Sidebar ========== -->
    <div class="sidebar bg-dark text-white" id="sidebar">
        <div class="sidebar-header">
            <img src="{{ asset('images/logodif.jpg') }}" alt="Logo DIF" class="sidebar-logo">
            <div class="sidebar-header-content">
                <div class="d-flex align-items-center">
                </div>
                <button class="btn btn-link text-white p-0 d-md-none" id="mobileSidebarClose">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </div>
        <ul class="nav flex-column px-2 pt-3">
            <div class="sidebar-section-title">Beneficiarios</div>
            <li class="nav-item mb-2">
                <a class="nav-link text-white {{ request()->routeIs('beneficiarios') ? 'active bg-secondary rounded' : '' }}" href="{{ route('beneficiarios') }}">
                    <i class="bi bi-person-lines-fill me-2"></i>
                    <span class="nav-link-text">Padrón</span>
                </a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link text-white" href="{{ route('administrador.importar_beneficiarios') }}">
                    <i class="bi bi-file-earmark-spreadsheet-fill me-2"></i>
                    <span class="nav-link-text">Importar</span>
                </a>
            </li>
            <div class="sidebar-section-title">Catálogos</div>
            <li class="nav-item mb-2">
                <a class="nav-link text-white {{ request()->is('administrador/usuarios*') ? 'active bg-secondary rounded' : '' }}" href="{{ route('usuarios.index') }}">
                    <i class="bi bi-person-gear me-2"></i>
                    <span class="nav-link-text">Usuarios</span>
                </a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link text-white" href="{{ route('administrador.areas') }}">
                    <i class="bi bi-diagram-3-fill me-2"></i>
                    <span class="nav-link-text">Áreas</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- ========== Navbar ========== -->
    <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top shadow-sm" id="navbar">
        <div class="container-fluid">
            <button class="btn btn-dark" id="sidebarToggle">
                <i class="bi bi-list"></i> Padrón de Beneficiarios
            </button>
            <div class="navbar-brand ms-3"></div>

            <!-- Menú derecho -->
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i> {{ Auth::user()->email }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    @endauth

    <!-- ========== Contenido Principal ========== -->
    <main class="main-content @auth py-4 @endauth" id="mainContent">
        @yield('content')
    </main>

    <!-- ========== JavaScripts ========== -->
    <script src="{{ mix('js/app.js') }}"></script>
    @auth
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const mobileSidebarClose = document.getElementById('mobileSidebarClose');
            const navbar = document.getElementById('navbar');
            const mainContent = document.getElementById('mainContent');
            let isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';

            // Aplicar estado inicial
            if (window.innerWidth >= 768) {
                applySidebarState(isCollapsed);
            }

            // Función para aplicar el estado del sidebar
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

            // Función para alternar el estado del sidebar
            function toggleSidebar() {
                isCollapsed = !isCollapsed;
                localStorage.setItem('sidebarCollapsed', isCollapsed);

                if (window.innerWidth >= 768) {
                    applySidebarState(isCollapsed);
                } else {
                    sidebar.classList.toggle('active');
                }
            }

            // Evento click del botón
            sidebarToggle.addEventListener('click', toggleSidebar);

            // Ajustar en redimensionamiento de pantalla
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
</body>

</html>