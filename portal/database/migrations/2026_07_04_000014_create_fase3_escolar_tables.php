<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grupos_escolares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ciclo_ingreso_id')->constrained('ciclos_ingreso')->cascadeOnDelete();
            $table->string('grupo', 50);
            $table->unsignedTinyInteger('semestre')->default(1);
            $table->foreignId('turno_id')->constrained('catalogos');
            $table->string('aula_base', 80)->nullable();
            $table->date('fecha_inicio_clases')->nullable();
            $table->text('indicaciones')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->unique(['ciclo_ingreso_id', 'grupo']);
        });

        Schema::table('procesos_ingreso', function (Blueprint $table) {
            $table->foreign('grupo_escolar_id')
                ->references('id')
                ->on('grupos_escolares')
                ->nullOnDelete();
        });

        Schema::create('horarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grupo_escolar_id')->constrained('grupos_escolares')->cascadeOnDelete();
            $table->unsignedTinyInteger('dia');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->string('materia', 100);
            $table->string('docente', 150)->nullable();
            $table->string('aula', 50)->nullable();
            $table->timestamps();

            $table->index(['grupo_escolar_id', 'dia', 'hora_inicio']);
        });

        Schema::create('sicobaem_config', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ciclo_ingreso_id')->constrained('ciclos_ingreso')->cascadeOnDelete();
            $table->string('url', 255)->nullable();
            $table->date('fecha_disponibilidad')->nullable();
            $table->text('pasos_activacion')->nullable();
            $table->string('contacto_soporte', 180)->nullable();
            $table->text('mensaje')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->unique('ciclo_ingreso_id');
        });

        Schema::create('regularizacion_alumno', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proceso_ingreso_id')->constrained('procesos_ingreso')->cascadeOnDelete();
            $table->foreignId('ruta_regularizacion_id')->nullable()->constrained('catalogos')->nullOnDelete();
            $table->string('plataforma_externa_url', 255)->nullable();
            $table->string('estatus', 30)->default('pendiente');
            $table->dateTime('fecha_asignacion')->nullable();
            $table->dateTime('fecha_ultima_consulta')->nullable();
            $table->timestamps();

            $table->unique('proceso_ingreso_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regularizacion_alumno');
        Schema::dropIfExists('sicobaem_config');
        Schema::dropIfExists('horarios');

        Schema::table('procesos_ingreso', function (Blueprint $table) {
            $table->dropForeign(['grupo_escolar_id']);
        });

        Schema::dropIfExists('grupos_escolares');
    }
};
