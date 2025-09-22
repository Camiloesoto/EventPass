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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion');
            $table->datetime('fecha_inicio');
            $table->datetime('fecha_fin');
            $table->integer('capacidad');
            $table->enum('estado', ['borrador', 'publicado', 'cancelado', 'completado'])->default('borrador');
            $table->timestamps();
            $table->softDeletes();

            // Índices para optimización
            $table->index(['estado', 'fecha_inicio']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
