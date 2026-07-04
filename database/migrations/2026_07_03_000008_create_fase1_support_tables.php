<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modulos_ciclo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ciclo_ingreso_id')->constrained('ciclos_ingreso')->cascadeOnDelete();
            $table->string('modulo', 50);
            $table->boolean('visible')->default(false);
            $table->dateTime('publicado_desde')->nullable();
            $table->foreignId('publicado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['ciclo_ingreso_id', 'modulo']);
        });

        Schema::create('descargas_formato', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proceso_ingreso_id')->constrained('procesos_ingreso')->cascadeOnDelete();
            $table->enum('tipo', ['generado', 'descargado_alumno', 'descargado_admin']);
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ip', 45)->nullable();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('importaciones_csv', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_importacion', 50);
            $table->string('archivo_original_path')->nullable();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('total_filas')->default(0);
            $table->unsignedInteger('registros_creados')->default(0);
            $table->unsignedInteger('registros_actualizados')->default(0);
            $table->unsignedInteger('registros_sin_cambios')->default(0);
            $table->unsignedInteger('registros_error')->default(0);
            $table->json('resumen')->nullable();
            $table->enum('estado', ['pendiente', 'procesando', 'completada', 'error'])->default('pendiente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('importaciones_csv');
        Schema::dropIfExists('descargas_formato');
        Schema::dropIfExists('modulos_ciclo');
    }
};
