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

        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@mail.com',
            'password' => bcrypt('1234567890'),
            // 'role' => 'admin',
        ]);

         User::factory()->create([
            'name' => 'user1',
            'email' => 'patient1@mail.com',
            'password' => bcrypt('1234567890'),

        ]);

         User::factory()->create([
            'name' => 'doctor1',
            'email' => 'doctor1@mail.com',
            'password' => bcrypt('1234567890'),
            // 'role' => 'doctor',
            'specialization_id' => 1,
        ]);

    }
}
