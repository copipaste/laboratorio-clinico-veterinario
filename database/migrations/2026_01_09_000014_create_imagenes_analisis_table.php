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
        Schema::create('imagenes_analisis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('analisis_id')->constrained('analisis')->onDelete('cascade');
            $table->string('nombre_archivo');
            $table->string('ruta_archivo');
            $table->text('descripcion')->nullable();
            $table->string('tipo_imagen');
            $table->integer('tamanio_kb');
            $table->foreignId('subido_por')->constrained('users')->onDelete('cascade');
            $table->timestamp('fecha_subida');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imagenes_analisis');
    }
};
