<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plantillas_omr', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 120);
            $table->string('examen_tipo', 40);
            $table->json('definicion_json');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('examenes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ciclo_ingreso_id')->constrained('ciclos_ingreso')->cascadeOnDelete();
            $table->string('nombre', 150);
            $table->enum('tipo', ['diagnostico_inicial', 'evaluacion_posterior'])->index();
            $table->date('fecha_aplicacion')->nullable();
            $table->string('version', 30)->nullable();
            $table->unsignedSmallInteger('total_preguntas')->default(0);
            $table->foreignId('plantilla_omr_id')->nullable()->constrained('plantillas_omr')->nullOnDelete();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('claves_respuesta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('examen_id')->constrained('examenes')->cascadeOnDelete();
            $table->unsignedSmallInteger('pregunta');
            $table->string('respuesta_correcta', 20);
            $table->foreignId('area_id')->constrained('catalogos');
            $table->foreignId('materia_id')->nullable()->constrained('catalogos')->nullOnDelete();
            $table->string('competencia', 150)->nullable();
            $table->decimal('ponderacion', 5, 2)->default(1);
            $table->timestamps();

            $table->unique(['examen_id', 'pregunta']);
        });

        Schema::create('hojas_respuesta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('examen_id')->constrained('examenes')->cascadeOnDelete();
            $table->foreignId('proceso_ingreso_id')->nullable()->constrained('procesos_ingreso')->nullOnDelete();
            $table->string('folio_examen', 20)->nullable();
            $table->string('imagen_original_path');
            $table->string('imagen_procesada_path')->nullable();
            $table->enum('estado_procesamiento', ['pendiente', 'procesada', 'requiere_revision', 'validada', 'exportada', 'error'])->default('pendiente');
            $table->decimal('confianza_lectura', 5, 2)->nullable();
            $table->text('observaciones')->nullable();
            $table->foreignId('procesado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('fecha_subida')->nullable();
            $table->timestamps();

            $table->index(['examen_id', 'estado_procesamiento']);
        });

        Schema::create('respuestas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hoja_respuesta_id')->constrained('hojas_respuesta')->cascadeOnDelete();
            $table->unsignedSmallInteger('pregunta');
            $table->char('respuesta_detectada', 1)->nullable();
            $table->char('respuesta_validada', 1)->nullable();
            $table->decimal('confianza', 5, 2)->nullable();
            $table->boolean('requiere_revision')->default(false);
            $table->boolean('corregida_manualmente')->default(false);
            $table->timestamps();

            $table->unique(['hoja_respuesta_id', 'pregunta']);
        });

        Schema::create('resultados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proceso_ingreso_id')->constrained('procesos_ingreso')->cascadeOnDelete();
            $table->foreignId('examen_id')->constrained('examenes')->cascadeOnDelete();
            $table->enum('origen', ['calculado', 'importado']);
            $table->decimal('puntaje_total', 6, 2)->default(0);
            $table->decimal('porcentaje_total', 5, 2)->default(0);
            $table->foreignId('nivel_riesgo_id')->constrained('catalogos');
            $table->foreignId('nivel_desempeno_id')->constrained('catalogos');
            $table->dateTime('fecha_calculo');
            $table->timestamps();

            $table->unique(['proceso_ingreso_id', 'examen_id']);
        });

        Schema::create('resultados_area', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resultado_id')->constrained('resultados')->cascadeOnDelete();
            $table->foreignId('area_id')->constrained('catalogos');
            $table->decimal('puntaje', 6, 2)->default(0);
            $table->decimal('porcentaje', 5, 2)->default(0);
            $table->foreignId('nivel_riesgo_id')->constrained('catalogos');
            $table->text('recomendacion')->nullable();
            $table->timestamps();

            $table->unique(['resultado_id', 'area_id']);
        });

        Schema::create('grupos_propedeuticos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ciclo_ingreso_id')->constrained('ciclos_ingreso')->cascadeOnDelete();
            $table->string('nombre', 50);
            $table->string('aula', 80)->nullable();
            $table->string('horario_texto', 180)->nullable();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->string('responsable', 150)->nullable();
            $table->text('indicaciones')->nullable();
            $table->text('materiales_requeridos')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->unique(['ciclo_ingreso_id', 'nombre']);
        });

        Schema::table('procesos_ingreso', function (Blueprint $table) {
            $table->foreign('grupo_propedeutico_id')
                ->references('id')
                ->on('grupos_propedeuticos')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('procesos_ingreso', function (Blueprint $table) {
            $table->dropForeign(['grupo_propedeutico_id']);
        });

        Schema::dropIfExists('grupos_propedeuticos');
        Schema::dropIfExists('resultados_area');
        Schema::dropIfExists('resultados');
        Schema::dropIfExists('respuestas');
        Schema::dropIfExists('hojas_respuesta');
        Schema::dropIfExists('claves_respuesta');
        Schema::dropIfExists('examenes');
        Schema::dropIfExists('plantillas_omr');
    }
};
