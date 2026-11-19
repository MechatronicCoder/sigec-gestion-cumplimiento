<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Migra los datos existentes antes de cambiar el enum
        $this->cambiarEstados(['abierta', 'cerrada', 'finalizada']);
        DB::table('evaluaciones')->where('estado', 'cerrada')->update(['estado' => 'finalizada']);
        $this->cambiarEstados(['abierta', 'finalizada']);
    }

    public function down(): void
    {
        $this->cambiarEstados(['abierta', 'cerrada', 'finalizada']);
        DB::table('evaluaciones')->where('estado', 'finalizada')->update(['estado' => 'cerrada']);
        $this->cambiarEstados(['abierta', 'cerrada']);
    }

    /**
     * Cambia los valores permitidos de evaluaciones.estado.
     *
     * En MySQL se mantiene el ALTER original; en otros motores (SQLite en las pruebas)
     * se usa el cambio de columna de Laravel.
     */
    private function cambiarEstados(array $estados): void
    {
        if (DB::getDriverName() === 'mysql') {
            $valores = implode(', ', array_map(fn (string $estado) => "'{$estado}'", $estados));
            DB::statement("ALTER TABLE evaluaciones MODIFY estado ENUM({$valores}) NOT NULL DEFAULT 'abierta'");

            return;
        }

        Schema::table('evaluaciones', function (Blueprint $table) use ($estados) {
            $table->enum('estado', $estados)->default('abierta')->change();
        });
    }
};
