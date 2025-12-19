<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Flush cache
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();

        // Create manager permissions
        $managerPermissions = [
            'user.read',
            'user.create',
            'user.update',
            'user.delete',

            'user.assign.role',
        ];

        foreach ($managerPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign existing permissions
        $managerRole = Role::firstOrCreate(['name' => 'Manager']);
        $managerRole->givePermissionTo($managerPermissions);

        Role::firstOrCreate(['name' => 'Medewerker']);
    }
}
