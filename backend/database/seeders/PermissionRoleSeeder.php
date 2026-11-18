<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionRoleSeeder extends Seeder
{
    public function run(): void
    {
        $permisos = [
            'gestionar-catalogo',
            'gestionar-usuarios',
            'ver-auditoria',
            'ver-checklist',
            'gestionar-checklist',
            'ver-reportes',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso, 'guard_name' => 'web']);
        }

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions($permisos);

        $evaluador = Role::firstOrCreate(['name' => 'evaluador', 'guard_name' => 'web']);
        $evaluador->syncPermissions(['ver-checklist', 'gestionar-checklist', 'ver-reportes']);

        $consulta = Role::firstOrCreate(['name' => 'consulta', 'guard_name' => 'web']);
        $consulta->syncPermissions(['ver-checklist', 'ver-reportes']);
    }
}
