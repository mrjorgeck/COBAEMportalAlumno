<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class DbBackupPredeploy extends Command
{
    protected $signature = 'db:backup-predeploy {--daily : Usa prefijo de respaldo diario}';

    protected $description = 'Genera un respaldo real de la base de datos MariaDB con mysqldump y aplica rotacion.';

    public function handle(): int
    {
        $connection = config('database.default');
        $database = config("database.connections.{$connection}.database");

        if (! in_array(config("database.connections.{$connection}.driver"), ['mysql', 'mariadb'], true)) {
            $this->error('El respaldo mysqldump solo aplica a conexiones mysql/mariadb.');

            return self::FAILURE;
        }

        $backupDir = $this->backupDirectory();
        File::ensureDirectoryExists($backupDir, 0700);

        $prefix = $this->option('daily') ? 'diario' : 'predeploy';
        $filename = sprintf('%s-%s-%s.sql', $prefix, $database, now()->format('Ymd-His'));
        $path = $backupDir.DIRECTORY_SEPARATOR.$filename;
        $defaultsFile = $this->writeDefaultsFile();

        try {
            $process = new Process([
                env('MYSQLDUMP_BIN', 'mysqldump'),
                '--defaults-extra-file='.$defaultsFile,
                '--single-transaction',
                '--quick',
                '--routines',
                '--triggers',
                $database,
            ]);
            $process->setTimeout(300);
            $process->run(function (string $type, string $buffer) use ($path): void {
                file_put_contents($path, $buffer, FILE_APPEND);
            });
        } finally {
            @unlink($defaultsFile);
        }

        if (! $process->isSuccessful() || ! file_exists($path) || filesize($path) === 0) {
            @unlink($path);
            $this->error('No se pudo generar el respaldo de BD.');
            $this->line($process->getErrorOutput());

            return self::FAILURE;
        }

        $this->rotateBackups($backupDir, $prefix, (int) env('DB_BACKUP_RETENTION', 14));
        $this->info("Respaldo generado: {$path}");

        return self::SUCCESS;
    }

    private function backupDirectory(): string
    {
        if ($path = env('DB_BACKUP_PATH')) {
            return $path;
        }

        $home = $_SERVER['HOME'] ?? $_SERVER['USERPROFILE'] ?? null;

        return $home ? $home.DIRECTORY_SEPARATOR.'backups' : storage_path('app/private/backups');
    }

    private function writeDefaultsFile(): string
    {
        $connection = config('database.default');
        $config = config("database.connections.{$connection}");
        $path = storage_path('app/private/mysqldump-'.bin2hex(random_bytes(8)).'.cnf');
        File::ensureDirectoryExists(dirname($path), 0700);

        $content = "[client]\n"
            .'host='.$this->optionValue((string) $config['host'])."\n"
            .'port='.(int) $config['port']."\n"
            .'user='.$this->optionValue((string) $config['username'])."\n"
            .'password='.$this->optionValue((string) $config['password'])."\n";

        file_put_contents($path, $content);
        @chmod($path, 0600);

        return $path;
    }

    private function optionValue(string $value): string
    {
        return '"'.str_replace(['\\', '"'], ['\\\\', '\\"'], $value).'"';
    }

    private function rotateBackups(string $backupDir, string $prefix, int $retention): void
    {
        $files = collect(File::glob($backupDir.DIRECTORY_SEPARATOR.$prefix.'-*.sql'))
            ->sortByDesc(fn (string $path) => filemtime($path))
            ->values();

        $files->slice(max(1, $retention))->each(fn (string $path) => File::delete($path));
    }
}
