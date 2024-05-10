<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        File::deleteDirectory(public_path('uploads'));

        $this->call([
            UserSeeder::class,
            ClientSeeder::class,
            WebsiteSeeder::class,
            PackageTypeSeeder::class,
        ]);

        Schema::enableForeignKeyConstraints();
    }
}