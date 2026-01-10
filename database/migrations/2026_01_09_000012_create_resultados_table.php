<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('resultados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('analisis_id')->constrained('analisis')->onDelete('cascade');
            $table->foreignId('parametro_id')->constrained('parametros_analisis')->onDelete('cascade');
            $table->string('valor');
            $table->boolean('fuera_rango')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resultados');
    }
};
