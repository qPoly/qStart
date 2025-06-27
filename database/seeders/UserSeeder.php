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
        $user = User::firstOrCreate(
            ['email' => 'info@qpoly.nl'],
            [
                'name' => 'qPoly',
                'password' => 'password',
            ]
        );

        if (!$user->hasRole('Manager')) {
            $user->assignRole('Manager');
        }
    }
}
