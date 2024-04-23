<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::truncate();

        $admin = User::create([
            'name' => 'Admin',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'alian@gmail.com',
            'password' => bcrypt('alian@123'),
            'profile_path' => '',
        ]);

        $this->storeProfileImage($admin);
    }

    public function storeProfileImage($admin)
    {
        $sourceFilePath = public_path('/images/profile.png');
        $destinationPath = public_path('/uploads/users/' . $admin->id);
        $fileName = time() . '.jpg';

        if (File::exists($destinationPath . '/' . $fileName)) {
            return 'File already exists!';
        }

        if (!File::isDirectory($destinationPath)) {
            File::makeDirectory($destinationPath, 0777, true, true);
        }

        File::copy($sourceFilePath, $destinationPath . '/' . $fileName);

        $admin->fill([
            'profile_path' => '/uploads/users/' . $admin->id . '/' . $fileName,
        ])->save();
    }
}