<?php

namespace App\Http\Controllers;

use App\Models\ProfileImage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Permission;

class LoginController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('auth/Login');
    }

    public function postLogin(Request $request): JsonResponse
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {

            return response()->json([
                'url' => session('url.intended'),
                'message' => 'Login successfully.',
                'is_success' => true
            ], 200);
        }

        return response()->json([
            'message' => 'Username or password incorrect.',
            'is_success' => false
        ], 200);
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->user();
        $user = User::where('email', $googleUser->email)->first();

        $google2fa = app('pragmarx.google2fa');
        $secret = $google2fa->generateSecretKey();

        if (!$user) {

            $user = User::create([
                'name' => $googleUser->name,
                'first_name' => $googleUser->user['given_name'],
                'last_name' => $googleUser->user['family_name'],
                'email' => $googleUser->email,
                'email_verified_at' => Carbon::now(),
                'password' => bcrypt($googleUser->user['given_name']),
                'google2fa_secret' => $secret,
            ]);

            $fileUrl = $googleUser->avatar;
            $fileContent = file_get_contents($fileUrl);
            $fileName = time() . '.png';

            Storage::disk('public')->put('uploads/users/' . $user->id . '/' . $fileName, $fileContent);
            $filePath = 'uploads/users/' . $user->id . '/' . $fileName;

            $profile_image = new ProfileImage;

            $profile_image->fill([
                'user_id' => $user->id,
                'file_name' => $fileName,
                'file_path' => $filePath,
                'file_extension' => '.jpg',
                'file_size' => 2000,
            ])->save();

            $permissions = Permission::all();

            foreach ($permissions as $permission) {
                $user->givePermissionTo($permission);
            }
        } else {
            $secret = $user->google2fa_secret;
        }

        $user->fill([
            'google2fa_secret' => $secret,
        ])->save();

        Auth::login($user, true);

        $QR_Image = $google2fa->getQRCodeInline(
            config('app.name'),
            Auth::user()->email,
            $secret,
        );

        return view('google2fa.register', ['QR_Image' => $QR_Image, 'secret' => $secret]);
    }

    public function logOut()
    {
        Session::flush();
        Auth::logout();

        return redirect('/login');
    }
}