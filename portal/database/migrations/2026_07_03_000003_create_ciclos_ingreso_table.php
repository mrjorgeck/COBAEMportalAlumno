<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ciclos_ingreso', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('anio')->unique(); // 2026
            $table->string('periodo_escolar', 10);          // "26-2"
            $table->string('generacion', 60);               // "Nuevo ingreso 2026"
            $table->boolean('activo')->default(false);
            // Ventana de registro/edición del alumno (SEG-06)
            $table->dateTime('registro_abierto_desde')->nullable();
            $table->dateTime('registro_abierto_hasta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ciclos_ingreso');
    }
};
