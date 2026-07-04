<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materiales_recomendados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained('catalogos');
            $table->foreignId('nivel_desempeno_id')->nullable()->constrained('catalogos')->nullOnDelete();
            $table->string('titulo', 180);
            $table->text('descripcion')->nullable();
            $table->string('url')->nullable();
            $table->string('archivo_path')->nullable();
            $table->enum('tipo_material', ['pdf', 'video', 'guia', 'actividad', 'sitio', 'curso_externo', 'plataforma_regularizacion']);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materiales_recomendados');
    }
};
