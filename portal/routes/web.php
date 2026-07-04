<?php

use App\Http\Controllers\Admin\AlumnoAdminController;
use App\Http\Controllers\Admin\AuditoriaController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\AvisoController as AdminAvisoController;
use App\Http\Controllers\Admin\CatalogoController;
use App\Http\Controllers\Admin\CicloController;
use App\Http\Controllers\Admin\CsvController;
use App\Http\Controllers\Admin\DashboardAcademicoController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExamenController;
use App\Http\Controllers\Admin\GrupoEscolarController;
use App\Http\Controllers\Admin\GrupoPropedeuticoController;
use App\Http\Controllers\Admin\MaterialRecomendadoController;
use App\Http\Controllers\Admin\ModuloController;
use App\Http\Controllers\Admin\SicobaemConfigController;
use App\Http\Controllers\Alumno\AccesoController;
use App\Http\Controllers\Alumno\FormatoController;
use App\Http\Controllers\Alumno\MiProcesoController;
use App\Http\Controllers\Alumno\RegistroController;
use App\Http\Controllers\Alumno\VerificacionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Portal del alumno (público, acceso por CURP)
|--------------------------------------------------------------------------
| Acceso CURP, registro y seguimiento del alumno.
*/

Route::view('/', 'alumno.landing')->name('alumno.landing');
Route::view('/aviso-de-privacidad', 'alumno.aviso-privacidad')->name('alumno.privacidad');
Route::redirect('/login', '/admin/login')->name('login');
Route::post('/acceso', [AccesoController::class, 'store'])->middleware('throttle:10,1')->name('alumno.acceso');
Route::post('/seleccionar-ciclo', [AccesoController::class, 'seleccionarCiclo'])->name('alumno.seleccionar-ciclo');
Route::get('/verificacion', [VerificacionController::class, 'create'])->middleware('alumno.sesion')->name('alumno.verificacion');
Route::post('/verificacion', [VerificacionController::class, 'store'])->middleware(['alumno.sesion', 'throttle:10,1'])->name('alumno.verificacion.store');
Route::get('/registro', [RegistroController::class, 'create'])->name('alumno.registro');
Route::post('/registro', [RegistroController::class, 'store'])->middleware('throttle:10,1')->name('alumno.registro.store');
Route::get('/registro/exito', [RegistroController::class, 'exito'])->middleware('alumno.sesion:sensible')->name('alumno.registro.exito');
Route::get('/mi-proceso', [MiProcesoController::class, 'index'])->middleware('alumno.sesion')->name('alumno.mi-proceso');
Route::get('/mi-proceso/formato/descargar', [FormatoController::class, 'alumno'])->middleware('alumno.sesion:sensible')->name('alumno.formato.descargar');
Route::get('/mi-proceso/{seccion}', [MiProcesoController::class, 'seccion'])->middleware(['alumno.sesion', 'modulo.publicado:seccion'])->name('alumno.mi-proceso.seccion');
Route::post('/mi-proceso/avisos/{aviso}/leido', [MiProcesoController::class, 'marcarAviso'])->middleware('alumno.sesion')->name('alumno.avisos.leido');
Route::post('/salir', [AccesoController::class, 'salir'])->name('alumno.salir');

/*
|--------------------------------------------------------------------------
| Panel administrativo
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    // Autenticación del personal
    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthController::class, 'create'])->name('login');
        Route::post('login', [AuthController::class, 'store'])
            ->middleware('throttle:5,1')
            ->name('login.store');
    });

    Route::middleware('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'destroy'])->name('logout');

        Route::get('/', DashboardController::class)
            ->middleware('permission:dashboard.registros')
            ->name('dashboard');
        Route::get('dashboard-academico', DashboardAcademicoController::class)
            ->middleware('permission:dashboard.academico')
            ->name('dashboard-academico');

        Route::get('alumnos', [AlumnoAdminController::class, 'index'])->middleware('permission:alumnos.ver')->name('alumnos.index');
        Route::get('alumnos/{proceso}', [AlumnoAdminController::class, 'show'])->middleware('permission:alumnos.ver')->name('alumnos.show');
        Route::patch('alumnos/{proceso}', [AlumnoAdminController::class, 'update'])->middleware('permission:alumnos.editar')->name('alumnos.update');
        Route::post('alumnos/{proceso}/matricula', [AlumnoAdminController::class, 'matricula'])->middleware('permission:grupos.asignar')->name('alumnos.matricula');
        Route::post('alumnos/{proceso}/grupo-propedeutico', [GrupoPropedeuticoController::class, 'asignar'])->middleware('permission:grupos.asignar')->name('alumnos.grupo-propedeutico');
        Route::post('alumnos/{proceso}/grupo-escolar', [GrupoEscolarController::class, 'asignar'])->middleware('permission:grupos.asignar')->name('alumnos.grupo-escolar');
        Route::post('alumnos/{proceso}/bloqueo', [AlumnoAdminController::class, 'bloquear'])->middleware('permission:alumnos.bloquear_edicion')->name('alumnos.bloquear');
        Route::patch('alumnos/{proceso}/documentos/{documento}', [AlumnoAdminController::class, 'actualizarDocumento'])->middleware('permission:documentacion.validar')->name('alumnos.documentos.update');
        Route::get('alumnos/{proceso}/formato', [FormatoController::class, 'admin'])->middleware('permission:formatos.descargar')->name('alumnos.formato');

        Route::get('exportaciones', [CsvController::class, 'exportaciones'])->middleware('permission:csv.exportar')->name('exportaciones');
        Route::get('exportaciones/alumnos', [CsvController::class, 'exportarAlumnos'])->middleware('permission:csv.exportar')->name('exportaciones.alumnos');
        Route::get('exportaciones/documentacion', [CsvController::class, 'exportarDocumentacion'])->middleware('permission:csv.exportar')->name('exportaciones.documentacion');
        Route::get('exportaciones/resultados', [CsvController::class, 'exportarResultados'])->middleware('permission:csv.exportar')->name('exportaciones.resultados');
        Route::get('importaciones', [CsvController::class, 'importaciones'])->middleware('permission:csv.importar')->name('importaciones');
        Route::get('importaciones/plantilla/{tipo}', [CsvController::class, 'plantilla'])->middleware('permission:csv.importar')->name('importaciones.plantilla');
        Route::post('importaciones', [CsvController::class, 'importar'])->middleware('permission:csv.importar')->name('importaciones.store');

        Route::resource('examenes', ExamenController::class)->middleware('permission:resultados.cargar')->except(['show', 'create', 'edit']);
        Route::resource('materiales', MaterialRecomendadoController::class)->middleware('permission:resultados.cargar')->except(['show', 'create', 'edit']);
        Route::resource('grupos-propedeuticos', GrupoPropedeuticoController::class)->middleware('permission:grupos.asignar')->except(['show', 'create', 'edit']);
        Route::resource('grupos-escolares', GrupoEscolarController::class)->middleware('permission:grupos.asignar')->except(['show', 'create', 'edit']);
        Route::post('grupos-escolares/{grupo}/horarios', [GrupoEscolarController::class, 'guardarHorario'])->middleware('permission:grupos.asignar')->name('grupos-escolares.horarios.store');
        Route::delete('horarios/{horario}', [GrupoEscolarController::class, 'eliminarHorario'])->middleware('permission:grupos.asignar')->name('horarios.destroy');
        Route::resource('avisos', AdminAvisoController::class)->middleware('permission:avisos.publicar')->except(['show']);
        Route::get('sicobaem', [SicobaemConfigController::class, 'index'])->middleware('permission:modulos.publicar')->name('sicobaem.index');
        Route::post('sicobaem', [SicobaemConfigController::class, 'store'])->middleware('permission:modulos.publicar')->name('sicobaem.store');
        Route::get('catalogos', [CatalogoController::class, 'index'])->middleware('permission:catalogos.administrar')->name('catalogos.index');
        Route::post('catalogos', [CatalogoController::class, 'store'])->middleware('permission:catalogos.administrar')->name('catalogos.store');
        Route::post('ciclos', [CicloController::class, 'store'])->middleware('permission:modulos.publicar')->name('ciclos.store');
        Route::get('modulos', [ModuloController::class, 'index'])->middleware('permission:modulos.publicar')->name('modulos.index');
        Route::post('modulos', [ModuloController::class, 'store'])->middleware('permission:modulos.publicar')->name('modulos.store');
        Route::get('auditoria', AuditoriaController::class)->middleware('permission:usuarios.administrar')->name('auditoria.index');
    });
});
