<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumnos', function (Blueprint $table) {
            $table->id();
            $table->char('curp', 18)->unique();
            $table->string('nombres', 100);
            $table->string('primer_apellido', 100);
            $table->string('segundo_apellido', 100)->nullable();
            $table->date('fecha_nacimiento');
            $table->foreignId('sexo_id')->constrained('catalogos');
            $table->foreignId('nacionalidad_id')->constrained('catalogos');
            $table->foreignId('estado_civil_id')->constrained('catalogos');
            $table->foreignId('entidad_nacimiento_id')->constrained('catalogos');
            $table->foreignId('municipio_nacimiento_id')->constrained('catalogos');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumnos');
    }
};
