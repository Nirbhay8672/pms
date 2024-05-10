<?php

namespace Database\Seeders;

use App\Models\PackageType;
use Illuminate\Database\Seeder;

class PackageTypeSeeder extends Seeder
{
    public function run(): void
    {
        PackageType::truncate();

        PackageType::create([
            'name' => 'Small',
        ]);

        PackageType::create([
            'name' => 'Medium',
        ]);

        PackageType::create([
            'name' => 'Big',
        ]);
    }
}