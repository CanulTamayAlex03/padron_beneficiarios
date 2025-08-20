<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\BeneficiarioController;


Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');

Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    // Cambiar la ruta para usar BeneficiarioController directamente
    Route::get('/beneficiarios', [BeneficiarioController::class, 'index'])->name('beneficiarios');

    // Rutas para importar/exportar beneficiarios ← Agregar estas rutas
    Route::prefix('administrador')->group(function () {
        Route::get('/importar_beneficiarios', [ExcelController::class, 'showImportForm'])
            ->name('administrador.importar_beneficiarios');
        
        Route::post('/importar_beneficiarios', [ExcelController::class, 'import'])
            ->name('administrador.importar_beneficiarios.process');
        
        Route::get('/exportar_beneficiarios', [ExcelController::class, 'export'])
            ->name('administrador.exportar_beneficiarios');
    });

    // Rutas para áreas
    Route::get('/administrador/areas', function () {
        return view('administrador.areas');
    })->name('administrador.areas');

    // Rutas para gestión de usuarios
    Route::prefix('administrador')->group(function () {
        Route::resource('usuarios', UsuarioController::class)
            ->except(['create', 'edit', 'show']);
    });
    Route::post('administrador/usuarios/{usuario}', [UsuarioController::class, 'update'])
    ->name('usuarios.update');
});