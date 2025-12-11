<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_create_assemblies_table.php
public function up(): void
{
    Schema::create('assemblies', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('description')->nullable();
        $table->dateTime('date');
        
        // pending: No ha empezado
        // in_progress: Se permiten escaneos (QR activo)
        // completed: Cerrada, ya no se modifica
        $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assemblies');
    }
};
