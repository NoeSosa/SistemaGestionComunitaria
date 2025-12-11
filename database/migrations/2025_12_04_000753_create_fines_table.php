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
    Schema::create('fines', function (Blueprint $table) {
        $table->id();
        $table->foreignId('assembly_id')->constrained()->cascadeOnDelete();
        $table->foreignId('citizen_id')->constrained()->cascadeOnDelete();
        
        $table->decimal('amount', 8, 2); // Monto pagado
        $table->text('notes')->nullable(); // Nota opcional
        $table->timestamp('paid_at')->useCurrent(); // Fecha de pago
        
        $table->timestamps();
        
        // Evitar cobrarle doble multa por la misma asamblea
        $table->unique(['assembly_id', 'citizen_id']); 
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fines');
    }
};
