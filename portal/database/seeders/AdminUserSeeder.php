<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // La contraseña se toma de ADMIN_INITIAL_PASSWORD en .env;
        // si no existe, se genera una aleatoria y se muestra UNA vez.
        $password = env('ADMIN_INITIAL_PASSWORD');

        if (blank($password)) {
            $password = Str::password(16);
            $this->command?->warn("Contraseña inicial del admin (guárdala y cámbiala): {$password}");
        }

        $admin = User::updateOrCreate(
            ['email' => 'admin@registrocobaemario.ariocentro.com'],
            [
                'name' => 'Administrador del Portal',
                'password' => $password,
                'activo' => true,
            ],
        );

        $admin->syncRoles(['admin']);
    }
}
