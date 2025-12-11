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
    Schema::create('settings', function (Blueprint $table) {
        $table->id();
        $table->string('key')->unique();
        $table->text('value')->nullable();
        $table->timestamps();
    });

    // Insertamos los datos por defecto de Huamelula
    DB::table('settings')->insert([
        ['key' => 'town_name', 'value' => 'Santa María Huamelula'],
        ['key' => 'town_address', 'value' => 'Palacio Municipal S/N, Centro'],
        ['key' => 'receipt_footer', 'value' => 'Gracias por su contribución al desarrollo del pueblo.'],
    ]);
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
