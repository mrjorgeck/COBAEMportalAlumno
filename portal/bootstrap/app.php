<?php

use App\Console\Commands\DbBackupPredeploy;
use App\Http\Middleware\ForzarHttpsProduccion;
use App\Http\Middleware\ModuloPublicado;
use App\Http\Middleware\RequerirCambioPassword;
use App\Http\Middleware\VerificarSesionAlumno;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withCommands([
        DbBackupPredeploy::class,
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(ForzarHttpsProduccion::class);

        $middleware->redirectGuestsTo(fn () => route('admin.login'));

        $middleware->alias([
            'alumno.sesion' => VerificarSesionAlumno::class,
            'modulo.publicado' => ModuloPublicado::class,
            'password.inicial' => RequerirCambioPassword::class,
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
