<?php

namespace Database\Seeders;

use App\Models\Specialization;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Roles;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@mail.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        Specialization::create([
            'name' => 'Spesialis Gigi',
        ]);
        Specialization::create([
            'name' => 'Spesialis Anak',
        ]);
        Specialization::create([
            'name' => 'Spesialis Jantung',
        ]);
        Specialization::create([
            'name' => 'Spesialis Kandungan',
        ]);


}
