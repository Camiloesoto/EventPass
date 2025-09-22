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
        Schema::create('waitlist_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('evento_id')->constrained('events')->onDelete('cascade');
            $table->enum('estado', ['esperando', 'notificado', 'convertido', 'expirado'])->default('esperando');
            $table->timestamp('fecha_notificacion')->nullable();
            $table->timestamps();

            // Índices para optimización
            $table->index(['evento_id', 'estado']);
            $table->index(['usuario_id', 'estado']);
            $table->unique(['evento_id', 'usuario_id']); // Un usuario solo puede estar una vez en la lista de espera por evento
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waitlist_entries');
    }
};
