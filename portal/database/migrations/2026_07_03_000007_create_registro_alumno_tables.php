<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('datos_contacto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proceso_ingreso_id')->unique()->constrained('procesos_ingreso')->cascadeOnDelete();
            $table->string('telefono', 30)->nullable();
            $table->string('celular', 30);
            $table->string('correo')->nullable();
            $table->foreignId('municipio_id')->constrained('catalogos');
            $table->foreignId('localidad_id')->constrained('catalogos');
            $table->string('colonia', 120)->nullable();
            $table->string('domicilio');
            $table->string('codigo_postal', 10)->nullable();
            $table->timestamps();
        });

        Schema::create('familiares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proceso_ingreso_id')->constrained('procesos_ingreso')->cascadeOnDelete();
            $table->enum('tipo_familiar', ['tutor', 'madre', 'padre', 'otro']);
            $table->string('nombres', 100);
            $table->string('primer_apellido', 100);
            $table->string('segundo_apellido', 100)->nullable();
            $table->string('telefono', 30)->nullable();
            $table->string('celular', 30)->nullable();
            $table->foreignId('ocupacion_id')->nullable()->constrained('catalogos')->nullOnDelete();
            $table->foreignId('estudios_id')->nullable()->constrained('catalogos')->nullOnDelete();
            $table->timestamps();

            $table->unique(['proceso_ingreso_id', 'tipo_familiar']);
        });

        Schema::create('otros_datos_alumno', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proceso_ingreso_id')->unique()->constrained('procesos_ingreso')->cascadeOnDelete();
            $table->string('no_seguro_medico')->nullable();
            $table->foreignId('beca_id')->nullable()->constrained('catalogos')->nullOnDelete();
            $table->decimal('estatura', 3, 2)->nullable();
            $table->decimal('peso', 5, 2)->nullable();
            $table->foreignId('tipo_sangre_id')->nullable()->constrained('catalogos')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('documentos_alumno', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proceso_ingreso_id')->constrained('procesos_ingreso')->cascadeOnDelete();
            $table->foreignId('tipo_documento_id')->constrained('catalogos');
            $table->string('estado_documento', 30)->default('pendiente');
            $table->text('observacion')->nullable();
            $table->dateTime('fecha_recepcion')->nullable();
            $table->foreignId('validado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('fecha_validacion')->nullable();
            $table->timestamps();

            $table->unique(['proceso_ingreso_id', 'tipo_documento_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos_alumno');
        Schema::dropIfExists('otros_datos_alumno');
        Schema::dropIfExists('familiares');
        Schema::dropIfExists('datos_contacto');
    }
};
