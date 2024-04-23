<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserProfileFormRequest;
use App\Http\Services\UserService;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function profile(): Response
    {
        $user = User::find(Auth::user()->id);

        $data = $user->only([
            'id',
            'profile_path',
            'name',
            'email',
            'first_name',
            'last_name',
        ]);

        return Inertia::render('user/profile/Index', [
            'user_details' => $data,
        ]);
    }

    public function updateProfile(UserProfileFormRequest $request, User $user, UserService $userService): JsonResponse
    {
        try {
            DB::beginTransaction();

            $userService->profileUpdate($request->all());
            $user->refresh();

            $data = $user->only([
                'id',
                'profile_path',
                'name',
                'email',
                'first_name',
                'last_name',
            ]);

            DB::commit();

            return $this->successResponse(message: "Your Profile has been updated.", data: [
                'user_details' => $data,
            ]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->errorResponse(message: $exception->getMessage());
        }
    }
}