<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Catálogo genérico administrable (docs/02 §6, CAT-01..08).
        Schema::create('catalogos', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 40)->index();
            $table->string('clave', 40);
            $table->string('nombre', 150);
            $table->string('descripcion')->nullable();
            $table->foreignId('parent_id')->nullable()
                ->constrained('catalogos')->nullOnDelete();
            $table->json('metadata')->nullable(); // ej. rangos de nivel_riesgo
            $table->unsignedSmallInteger('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->unique(['tipo', 'clave']); // CAT-06
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalogos');
    }
};
