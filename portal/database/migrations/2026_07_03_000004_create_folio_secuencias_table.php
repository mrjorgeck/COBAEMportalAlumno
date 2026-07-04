<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Secuencia de folios internos por ciclo+plantel.
        // SOLO FolioService escribe aquí (transacción + lockForUpdate).
        Schema::create('folio_secuencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ciclo_ingreso_id')->constrained('ciclos_ingreso');
            $table->foreignId('plantel_id')->constrained('planteles');
            $table->unsignedInteger('consecutivo')->default(0);
            $table->timestamps();

            $table->unique(['ciclo_ingreso_id', 'plantel_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('folio_secuencias');
    }
};
