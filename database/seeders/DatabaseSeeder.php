<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesPermisosSeeder::class,
            CatalogoSeeder::class,
            EntidadesMunicipiosSeeder::class,
            PlantelCicloSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
