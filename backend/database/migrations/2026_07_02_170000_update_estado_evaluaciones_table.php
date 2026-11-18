<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
//use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Migra los datos existentes antes de cambiar el enum
        DB::table('evaluaciones')->where('estado', 'cerrada')->update(['estado' => 'finalizada']);

        DB::statement("ALTER TABLE evaluaciones MODIFY estado ENUM('abierta', 'finalizada') NOT NULL DEFAULT 'abierta'");
    }

    public function down(): void
    {
        DB::table('evaluaciones')->where('estado', 'finalizada')->update(['estado' => 'cerrada']);
        DB::statement("ALTER TABLE evaluaciones MODIFY estado ENUM('abierta', 'cerrada') NOT NULL DEFAULT 'abierta'");
    }
};
