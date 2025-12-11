<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_create_citizens_table.php
public function up(): void
{
    Schema::create('citizens', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('curp', 18)->unique(); // Indispensable para el QR
        
        // Datos de contacto (Futuro módulo de agua y notificaciones)
        $table->string('phone')->nullable();
        $table->string('address')->nullable();     // Calle y número
        $table->string('neighborhood')->nullable();// Colonia
        
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citizens');
    }
};
