<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        Client::truncate();

        Client::create([
            'name' => 'Rashmin Patel',
            'phone_number' => '82200458545',
            'email' => 'rashminpatel@gmail.com',
            'joining_date' => now(),
        ]);

        Client::create([
            'name' => 'Kuldip Koradiya',
            'phone_number' => '9979245856',
            'email' => 'kuldipkoradiya@gmail.com',
            'joining_date' => now(),
        ]);

        Client::create([
            'name' => 'Nirbhay Hathaliya',
            'phone_number' => '9979255859',
            'email' => 'nirbhayhathaliya@gmail.com',
            'joining_date' => now(),
        ]);

        Client::create([
            'name' => 'Vishal Aghera',
            'phone_number' => '7782546958',
            'email' => 'vishalaghera@gmail.com',
            'joining_date' => now(),
        ]);

        Client::create([
            'name' => 'Devendra Mer',
            'phone_number' => '8782546958',
            'email' => 'devendramer@gmail.com',
            'joining_date' => now(),
        ]);
    }
}