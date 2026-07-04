<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('avisos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('mensaje');
            $table->foreignId('tipo_aviso_id')->constrained('catalogos');
            $table->enum('prioridad', ['informativo', 'importante', 'urgente'])->default('informativo');
            $table->dateTime('fecha_inicio')->nullable();
            $table->dateTime('fecha_fin')->nullable();
            $table->enum('dirigido_a', ['todos', 'ciclo', 'grupo_propedeutico', 'grupo_escolar', 'alumno'])->default('todos');
            $table->foreignId('ciclo_ingreso_id')->nullable()->constrained('ciclos_ingreso')->nullOnDelete();
            $table->unsignedBigInteger('grupo_propedeutico_id')->nullable()->index();
            $table->unsignedBigInteger('grupo_escolar_id')->nullable()->index();
            $table->foreignId('alumno_id')->nullable()->constrained('alumnos')->nullOnDelete();
            $table->string('url_o_archivo')->nullable();
            $table->boolean('visible')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('alumno_avisos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('alumnos')->cascadeOnDelete();
            $table->foreignId('aviso_id')->constrained('avisos')->cascadeOnDelete();
            $table->boolean('leido')->default(false);
            $table->dateTime('fecha_lectura')->nullable();
            $table->timestamps();

            $table->unique(['alumno_id', 'aviso_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumno_avisos');
        Schema::dropIfExists('avisos');
    }
};
