<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planteles', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 20)->unique();      // "ARIO" (parte del folio)
            $table->string('nombre', 150);
            $table->string('clave_oficial', 30)->nullable(); // clave SEP/COBAEM
            $table->string('direccion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planteles');
    }
};
