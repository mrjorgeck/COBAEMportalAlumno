<?php

namespace App\Providers;

use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Producción en hosting compartido: evitar errores por índices largos
        // en utf8mb4 con versiones antiguas de MariaDB.
        Builder::defaultStringLength(191);

        // En producción, registrar consultas lentas para diagnóstico.
        if ($this->app->isProduction()) {
            DB::listen(function ($query) {
                if ($query->time > 1000) {
                    logger()->warning('Consulta lenta', [
                        'sql' => $query->sql,
                        'ms' => $query->time,
                    ]);
                }
            });
        }
    }
}
