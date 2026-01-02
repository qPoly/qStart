<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create "admin"
        $admin = User::firstOrCreate(
            ['email' => 'info@qpoly.nl'],
            [
                'name' => 'qPoly',
                'password' => 'password',
            ]
        );

        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        // Create "user"
        $user = User::firstOrCreate(
            ['email' => 'user@qpoly.nl'],
            [
                'name' => 'User',
                'password' => 'password',
            ]
        );

        if (!$user->hasRole('user')) {
            $user->assignRole('user');
        }

        $users = User::factory()->count(9)->create();

        foreach ($users as $user) {
            $user->assignRole('user');
        }
    }
}
