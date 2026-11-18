<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluacion_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluacion_id')
                  ->constrained('evaluaciones')
                  ->onDelete('cascade');
            $table->foreignId('item_id')
                  ->constrained('items')
                  ->onDelete('cascade');
            $table->decimal('calificacion', 5, 2)->nullable();
            $table->text('observaciones')->nullable();
            $table->foreignId('evaluador_id')
                  ->constrained('users')
                  ->onDelete('restrict');
            $table->timestamp('fecha_registro')->useCurrent();
            $table->timestamps();

            $table->unique(['evaluacion_id', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluacion_items');
    }
};
