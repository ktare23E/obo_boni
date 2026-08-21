<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'first_name' => 'Jhon Edmar',
            'last_name' => 'Palantang',
            'user_type' => 'Admin',
            'status' => 'Active',
            'email' => 'edselpaler85@gmail.com',
        ]);
    }
}
