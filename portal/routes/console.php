<?php

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Tareas programadas (cron de Hostinger ejecuta schedule:run cada minuto)
|--------------------------------------------------------------------------
| Sin workers persistentes en hosting compartido: la cola se procesa
| cada minuto hasta vaciarse o agotar 50 segundos (docs/08).
*/

Schedule::command('queue:work --stop-when-empty --max-time=50 --tries=3')
    ->everyMinute()
    ->withoutOverlapping();

Schedule::command('queue:prune-failed --hours=168')->daily();
Schedule::command('auth:clear-resets')->daily();
Schedule::command('db:backup-predeploy --daily')->dailyAt('02:15');
