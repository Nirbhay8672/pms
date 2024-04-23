<?php

declare(strict_types=1);

namespace App\Http\Services;

use App\Models\ProfileImage;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UserService
{
    public function profileUpdate(array $usersDetails): void
    {
        $user = User::find((int) $usersDetails['user_id']);

        $user->fill([
            'name' => $usersDetails['name'],
            'first_name' => $usersDetails['first_name'],
            'last_name' => $usersDetails['last_name'],
            'email' => $usersDetails['email'],
        ]);

        if (!empty($usersDetails['password'])) {
            $user->password = bcrypt($usersDetails['password']);
        }

        $user->save();

        if ($usersDetails['profile_image'] ?? false) {
            $this->storeProfileImage($usersDetails['profile_image'], $user);
        }
    }

    public function storeProfileImage(UploadedFile $file, User $user): void
    {
        $user->loadMissing('profileImage');

        $profile_image = new ProfileImage();

        if ($user->profileImage != null) {
            Storage::disk('public')->delete($user->profileImage['file_path']);
            $profile_image = ProfileImage::find((int) $user->profileImage['id']);
        }

        $file_name = time() . '.' . $file->getClientOriginalExtension();
        $file_path = "uploads/users/{$user->id}";

        Storage::disk('public')->putFileAs("uploads/users/{$user->id}", $file, $file_name);

        $profile_image->fill([
            'user_id' => $user->id,
            'file_name' => $file_name,
            'file_path' => $file_path . '/' . $file_name,
            'file_extension' => $file->extension(),
            'file_size' => $file->getSize(),
        ])->save();
    }
}