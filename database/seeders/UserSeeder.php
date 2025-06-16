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
        if (!User::where('email', 'info@qpoly.nl')->exists()) {
            User::factory()->create([
                'name' => 'qPoly',
                'email' => 'info@qpoly.nl',
                'password' => 'password',
            ]);
        }
    }
}
