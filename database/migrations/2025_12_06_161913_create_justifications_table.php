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
    Schema::create('justifications', function (Blueprint $table) {
        $table->id();
        $table->foreignId('assembly_id')->constrained()->cascadeOnDelete();
        $table->foreignId('citizen_id')->constrained()->cascadeOnDelete();
        $table->text('reason'); // Motivo (Enfermedad, Comisión, etc)
        $table->timestamps();

        // Un ciudadano solo se justifica una vez por asamblea
        $table->unique(['assembly_id', 'citizen_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('justifications');
    }
};
