<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Padron de Beneficiarios</title>
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.css') }}">
     <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #8BC34A;
        }
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-container {
            width: 100%;
            max-width: 1000px;
        }
        .login-card {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: none;
            overflow: hidden;
        }
        .card-left {
            background: linear-gradient(to bottom right, var(--primary-color), #1a2530);
            color: white;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .system-name {
            font-size: 35px;
            font-weight: 600;
            margin-bottom: 1rem;
            text-align: center;
        }
        .system-description {
            font-size: 1rem;
            line-height: 1.6;
            opacity: 0.9;
            text-align: center;
            margin-bottom: 2rem;
        }
        .btn-login {
            background-color: var(--secondary-color);
            color: white;
            transition: all 0.3s;
            border: none;
            padding: 10px 20px;
            font-weight: 500;
        }
        .btn-login:hover {
            background-color: #7CB342;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.25rem rgba(139, 195, 74, 0.25);
        }
        .input-group-text {
            background-color: white;
            border-right: none;
        }
        .form-control {
            border-left: none;
        }
        .logo-img {
            max-width: 250px;
            margin-bottom: 1.5rem;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        @media (max-width: 768px) {
            .card-left {
                display: none;
            }
            
            .login-card {
                margin: 0 15px;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="row g-0">
            <!-- Panel izquierdo con información del sistema -->
            <div class="col-lg-5 d-none d-lg-block">
                <div class="card-left h-100">
                    <img src="{{ asset('images/logodif.jpg') }}" alt="Logo DIF" class="logo-img">
                    <div class="system-name">Sistema DIF Yucatán</div>
                    <p class="system-description">
                        Acceda al Padrón Único de Beneficiarios para gestionar la información de los programas de asistencia social.
                    </p>

                </div>
            </div>
            <!-- Panel derecho con formulario de login -->
            <div class="col-lg-7">
                <div class="card login-card h-100">
                    <div class="card-body p-5">
                        <div class="text-center mb-4 d-block d-lg-none">
                            <img src="{{ asset('images/logodif.jpg') }}" alt="Logo DIF" class="logo-img">
                        </div>

                        <h4 class="text-center mb-1">Bienvenido</h4>
                        <p class="text-center text-muted mb-4">Ingrese sus credenciales para acceder al sistema</p>

                        <form method="POST" action="/login">
                            @csrf
                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Correo electrónico</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white">
                                        <i class="bi bi-person"></i>
                                    </span>
                                    <input type="email"
                                        class="form-control"
                                        id="email"
                                        name="email"
                                        placeholder="Ingrese su correo electrónico"
                                        required autofocus>
                                </div>
                            </div>

                            <!-- Contraseña -->
                            <div class="mb-4">
                                <label for="password" class="form-label">Contraseña</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white">
                                        <i class="bi bi-lock"></i>
                                    </span>
                                    <input type="password"
                                        class="form-control"
                                        id="password"
                                        name="password"
                                        placeholder="Ingrese su contraseña"
                                        required>
                                </div>
                            </div>

                            <!-- Recordar usuario -->
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember">
                                <label class="form-check-label" for="remember">Recordar mis datos</label>
                            </div>

                            <!-- Botón de login -->
                            <div class="d-grid gap-2 mb-3">
                                <button type="submit" class="btn btn-login btn-lg">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar sesión
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>