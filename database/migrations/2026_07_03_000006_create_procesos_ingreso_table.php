<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procesos_ingreso', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('alumnos');
            $table->foreignId('ciclo_ingreso_id')->constrained('ciclos_ingreso');
            $table->foreignId('plantel_id')->constrained('planteles');
            $table->string('folio_registro', 30)->unique();
            $table->string('folio_examen', 20)->nullable();
            $table->unsignedTinyInteger('semestre_solicitado')->default(1);
            $table->foreignId('tipo_estudiante_id')->constrained('catalogos');
            $table->foreignId('paraescolar_id')->nullable()->constrained('catalogos')->nullOnDelete();
            $table->foreignId('secundaria_procedencia_id')->nullable()->constrained('catalogos')->nullOnDelete();
            $table->foreignId('entidad_secundaria_id')->nullable()->constrained('catalogos')->nullOnDelete();
            $table->foreignId('municipio_secundaria_id')->nullable()->constrained('catalogos')->nullOnDelete();
            $table->foreignId('tipo_secundaria_id')->nullable()->constrained('catalogos')->nullOnDelete();
            $table->foreignId('turno_secundaria_id')->nullable()->constrained('catalogos')->nullOnDelete();
            $table->decimal('promedio_secundaria', 4, 2)->default(0);
            $table->unsignedBigInteger('grupo_propedeutico_id')->nullable()->index();
            $table->unsignedBigInteger('grupo_escolar_id')->nullable()->index();
            $table->string('matricula', 20)->nullable()->unique();
            $table->string('estatus_proceso', 30)->default('registro_incompleto')->index();
            $table->string('estatus_documentacion', 30)->default('pendiente');
            $table->boolean('edicion_bloqueada')->default(false);
            $table->string('plantilla_pdf_version', 10)->default('v2026');
            $table->dateTime('acepto_privacidad_at')->nullable();
            $table->dateTime('fecha_registro')->nullable();
            $table->dateTime('fecha_validacion')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['alumno_id', 'ciclo_ingreso_id']);
            $table->unique(['folio_examen', 'ciclo_ingreso_id']);
            $table->index(['ciclo_ingreso_id', 'estatus_proceso']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procesos_ingreso');
    }
};
