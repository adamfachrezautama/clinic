<?php

namespace Database\Seeders;

use App\Models\Clinic;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClinicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Clinic::create([
            'name' => 'Klinik Sehat',
            'address' => 'Jl. Sehat No. 1, Jakarta',
            'phone' => '021-12345678',
            'email' => '',
            'website' => '',
            'opening_time' => '08:00:00',
            'closing_time' => '17:00:00',
            'description' => 'Klinik Sehat menyediakan layanan kesehatan umum dan spesialis.',
            'spesialis' => 'Umum',
        ]);
    }
}
