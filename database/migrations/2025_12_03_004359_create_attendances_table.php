<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_create_attendances_table.php
public function up(): void
{
    Schema::create('attendances', function (Blueprint $table) {
        $table->id();
        
        // Relaciones
        $table->foreignId('assembly_id')->constrained()->cascadeOnDelete();
        $table->foreignId('citizen_id')->constrained()->cascadeOnDelete();
        
        // Tiempos
        $table->timestamp('check_in_at')->useCurrent(); // Hora de llegada
        $table->timestamp('quorum_check_at')->nullable(); // Hora de revalidación (salida/permanencia)
        
        // Candado: Un ciudadano solo puede tener UN registro por asamblea
        $table->unique(['assembly_id', 'citizen_id']);
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
