<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@sigec.local'],
            [
                'name' => 'Administrador SIGEC',
                'password' => Hash::make('CambiaEstaClave123'),
                'activo' => true,
            ]
        );
        $admin->syncRoles(['admin']);

        $evaluador = User::firstOrCreate(
            ['email' => 'evaluador@sigec.local'],
            [
                'name' => 'Evaluador SIGEC',
                'password' => Hash::make('CambiaEstaClave123'),
                'activo' => true,
            ]
        );
        $evaluador->syncRoles(['evaluador']);

        $consulta = User::firstOrCreate(
            ['email' => 'consulta@sigec.local'],
            [
                'name' => 'Consulta SIGEC',
                'password' => Hash::make('CambiaEstaClave123'),
                'activo' => true,
            ]
        );
        $consulta->syncRoles(['consulta']);
    }
}
