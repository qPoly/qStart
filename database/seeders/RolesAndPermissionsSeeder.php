<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Flush cache
        Artisan::call('permission:cache-reset');

        // Define permissions per role
        $adminPermissions = [
            'manage users',
            'manage organisations',
        ];

        $userPermissions = [
            'manage users',
        ];

        // Create all permissions
        $allPermissions = array_unique(array_merge($adminPermissions, $userPermissions));

        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        Role::firstOrCreate(['name' => 'admin'])->syncPermissions($adminPermissions);
        Role::firstOrCreate(['name' => 'user'])->syncPermissions($userPermissions);

        // Make sure that user "info@qpoly.nl" has the admin role
        $user = User::where('email', 'info@qpoly.nl')->first();

        if ($user) {
            $user->assignRole('admin');
        }

        // Make sure that every user without a role, gets the user role
        $users = User::whereDoesntHave('roles')->get();

        foreach ($users as $user) {
            $user->assignRole('user');
        }
    }
}
