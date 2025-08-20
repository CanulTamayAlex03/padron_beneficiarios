<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema DIF</title>
    <!-- Bootstrap CSS -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.css') }}">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        
        .login-container {
            width: 100%;
            max-width: 1200px;
        }
        
        .login-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .btn-login {
            background-color: #8BC34A;
            color: white;
            transition: all 0.3s;
        }
        
        .btn-login:hover {
            background-color: #7CB342;
            color: white;
        }
        
        .logo-img {
            max-width: 300px;
            width: 100%;
            height: auto;
        }
        
        .title-text {
            font-size: 2.5rem;
            font-weight: 400;
            font-family: 'Arial', sans-serif;
            color: #333;
        }
        
        @media (max-width: 768px) {
            .title-text {
                font-size: 1.8rem;
                text-align: center !important;
                margin-top: 20px !important;
            }
            
            .logo-container {
                text-align: center !important;
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Logo -->
        <div class="row mb-4">
            <div class="col-md-6 logo-container">
                <img src="{{ asset('images/logodif.jpg') }}" alt="Logo DIF" class="logo-img">
            </div>
        </div>
        
        <!-- Contenedor del login -->
        <div class="row justify-content-center align-items-center">
            <!-- Tarjeta de login -->
            <div class="col-lg-5 col-md-6 mb-4 mb-md-0">
                <div class="card login-card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h5 class="text-center mb-4">Iniciar Sesión</h5>
                        <form method="POST" action="/login">
                            @csrf
                            <!-- Email -->
                            <div class="mb-3 input-group">
                                <span class="input-group-text bg-white">
                                    <i class="bi bi-person"></i>
                                </span>
                                <input type="email" 
                                       class="form-control" 
                                       name="email" 
                                       placeholder="Email" 
                                       required autofocus>
                            </div>

                            <!-- Contraseña -->
                            <div class="mb-3 input-group">
                                <span class="input-group-text bg-white">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input type="password" 
                                       class="form-control" 
                                       name="password" 
                                       placeholder="Contraseña" 
                                       required>
                            </div>

                            <!-- Botón de login -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-login">
                                    Iniciar sesión
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Texto descriptivo -->
            <div class="col-lg-5 col-md-6">
                <h2 class="title-text">
                    Padrón Único de Beneficiarios de Yucatán
                </h2>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>