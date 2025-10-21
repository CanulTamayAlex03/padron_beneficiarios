<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\BeneficiarioController;
use App\Http\Controllers\BeneficiarioFamiliarController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\EstudioSocioeconomicoController;
use App\Http\Controllers\IntegranteHogarController;
use App\Http\Controllers\LineaConevalController;

// ================== LOGIN / LOGOUT ==================
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ================== RUTAS PROTEGIDAS ==================
Route::middleware('auth')->group(function () {

    // ================== BENEFICIARIOS ==================
    Route::middleware('permission:ver beneficiarios')->group(function () {
        Route::get('/beneficiarios/check-curp', [BeneficiarioController::class, 'checkCurp'])
            ->name('beneficiarios.check-curp');
        Route::get('/beneficiarios', [BeneficiarioController::class, 'index'])->name('beneficiarios');
        Route::get('/beneficiarios/{beneficiario}', [BeneficiarioController::class, 'show'])->name('beneficiarios.show');

        // Editar solo el beneficiario (sin estudios)
        Route::get('/beneficiarios/{beneficiario}/editar', [BeneficiarioController::class, 'editarBeneficiario'])
            ->name('beneficiarios.editar');
    });

    Route::post('/beneficiarios', [BeneficiarioController::class, 'store'])
        ->middleware('permission:crear beneficiarios')
        ->name('beneficiarios.store');

    Route::put('/beneficiarios/{beneficiario}', [BeneficiarioController::class, 'update'])
        ->middleware('permission:editar beneficiarios')
        ->name('beneficiarios.update');

    Route::delete('/beneficiarios/{beneficiario}', [BeneficiarioController::class, 'destroy'])
        ->middleware('permission:eliminar beneficiarios')
        ->name('beneficiarios.destroy');

    // ================== FAMILIARES DE BENEFICIARIOS ==================

    Route::post('beneficiarios/{beneficiario}/familiares', [BeneficiarioFamiliarController::class, 'store'])
        ->name('familiares.store');

    Route::put('familiares/{familiar}', [BeneficiarioFamiliarController::class, 'update'])
        ->name('familiares.update');

    Route::delete('familiares/{familiar}', [BeneficiarioFamiliarController::class, 'destroy'])
        ->name('familiares.destroy');

    // ================== ESTUDIO SOCIOECONOMICO ==================
    Route::get('estudios', [EstudioSocioeconomicoController::class, 'index'])->name('estudios.index');
    Route::get('estudios/create/{beneficiario}', [EstudioSocioeconomicoController::class, 'create'])->name('estudios.create');
    Route::post('estudios', [EstudioSocioeconomicoController::class, 'store'])->name('estudios.store');

    // Ruta para editar un estudio específico junto con el beneficiario
    Route::get('beneficiarios/{beneficiario}/estudios/{estudio}/editar', [EstudioSocioeconomicoController::class, 'editarCompleto'])
        ->name('beneficiarios.estudios.editar');

    Route::put('estudios/{estudio}', [EstudioSocioeconomicoController::class, 'update'])->name('estudios.update');

    Route::post('estudios/{estudio}/update-linea-coneval', [EstudioSocioeconomicoController::class, 'updateLineaConeval'])
        ->name('estudios.update-linea-coneval');

    Route::post('estudios/{estudio}/update-coneval', [EstudioSocioeconomicoController::class, 'updateConeval'])
        ->name('estudios.update-coneval');

    // ================== INTEGRANTES DEL HOGAR ==================
    Route::post('integrantes-hogar', [IntegranteHogarController::class, 'store'])
        ->name('integrantes-hogar.store')
        ->middleware('auth');

    Route::put('integrantes-hogar/{integrante}', [IntegranteHogarController::class, 'update'])
        ->name('integrantes-hogar.update')
        ->middleware('auth');

    Route::delete('integrantes-hogar/{integrante}', [IntegranteHogarController::class, 'destroy'])
        ->name('integrantes-hogar.destroy')
        ->middleware('auth');

    // ================== LINEAS CONEVAL ==================
    Route::resource('lineas-coneval', LineaConevalController::class);

    Route::get('lineas-coneval/por-periodo', [LineaConevalController::class, 'getByPeriodo'])
        ->name('lineas-coneval.por-periodo');

    // Rutas para resultados de estudios
    Route::get('/beneficiarios/{beneficiario}/estudios-completos', [BeneficiarioController::class, 'getEstudiosCompletos']);
    Route::get('/estudios/{estudio}/vista-resultado', [EstudioSocioeconomicoController::class, 'vistaResultado']);


    // ================== PANEL ADMINISTRADOR ==================
    Route::prefix('administrador')
        ->middleware('permission:acceder panel administracion')
        ->group(function () {

            // ================== USUARIOS ==================
            Route::get('/usuarios', [UsuarioController::class, 'index'])
                ->middleware('permission:ver usuarios')
                ->name('usuarios.index');

            Route::get('/usuarios/{usuario}/ajax', [UsuarioController::class, 'editAjax'])
                ->middleware('permission:editar usuarios')
                ->name('usuarios.editAjax');

            Route::post('/usuarios', [UsuarioController::class, 'store'])
                ->middleware('permission:crear usuarios')
                ->name('usuarios.store');

            Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update'])
                ->middleware('permission:editar usuarios')
                ->name('usuarios.update');

            Route::delete('/usuarios/{usuario}', [UsuarioController::class, 'destroy'])
                ->middleware('permission:eliminar usuarios')
                ->name('usuarios.destroy');

            // ================== IMPORTAR / EXPORTAR BENEFICIARIOS ==================
            Route::get('/importar_beneficiarios', [ExcelController::class, 'showImportForm'])
                ->middleware('permission:importar beneficiarios')
                ->name('administrador.importar_beneficiarios');

            Route::post('/importar_beneficiarios', [ExcelController::class, 'import'])
                ->middleware('permission:importar beneficiarios')
                ->name('administrador.importar_beneficiarios.process');

            Route::get('/exportar_beneficiarios', [ExcelController::class, 'export'])
                ->middleware('permission:exportar beneficiarios')
                ->name('administrador.exportar_beneficiarios');

            // ================== AREAS ==================
            Route::get('/areas', function () {
                return view('administrador.areas');
            })->middleware('permission:ver areas')
                ->name('administrador.areas');
        });

    // ================== ROLES (SOLO SUPERADMIN) ==================
    Route::middleware(['role:superadmin', 'permission:gestionar roles'])->group(function () {
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
        Route::get('/roles/{id}/ajax-edit', [RoleController::class, 'editAjax'])->name('roles.editAjax');
    });
});
