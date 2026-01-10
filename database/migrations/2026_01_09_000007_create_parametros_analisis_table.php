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
        Schema::create('parametros_analisis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_analisis_id')->constrained('tipos_analisis')->onDelete('cascade');
            $table->string('nombre');
            $table->string('unidad');
            $table->integer('orden');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parametros_analisis');
    }
};
