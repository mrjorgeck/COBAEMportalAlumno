<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('claves_respuesta')) {
            return;
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE claves_respuesta MODIFY respuesta_correcta VARCHAR(20) NOT NULL');
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('claves_respuesta')) {
            return;
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE claves_respuesta MODIFY respuesta_correcta CHAR(1) NOT NULL');
        }
    }
};
