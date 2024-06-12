<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PluginController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Auth::routes();

Route::get('auth/google', [LoginController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [LoginController::class, 'handleGoogleCallback']);

Route::get('/complete-registration', [RegisterController::class, 'completeRegistration'])->name('complete.registration');

Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/logout-auth', [LoginController::class, 'logOut']);
});

Route::middleware(['2fa', 'auth'])->group(function () {
    Route::get('/home', [DashboardController::class, 'index'])->name('home');

    Route::post('/2fa', function () {
        return redirect(route('home'));
    })->name('2fa');
});

// users url
Route::prefix('users')->as('users.')->middleware(['auth', '2fa'])->group(function () {
    Route::get('/profile', [UserController::class, 'profile'])->middleware(['permission:view_profile'])->name('profile');
    Route::post('/update-profile/{user}', [UserController::class, 'updateProfile'])->middleware(['permission:update_profile'])->name('update_profile');
});

// members url  
Route::prefix('members')->as('members.')->middleware(['auth', '2fa'])->group(function () {
    Route::get('/index', [MemberController::class, 'index'])->middleware(['permission:view_members'])->name('member_index');
    Route::post('/datatable', [MemberController::class, 'datatable'])->middleware(['permission:view_members'])->name('member_datatable');
    Route::post('/create-or-update/{member?}', [MemberController::class, 'createOrUpdate'])->name('create_or_update_meber');
    Route::get('/delete/{member}', [MemberController::class, 'delete'])->name('delete_member');
    Route::post('/delete-members', [MemberController::class, 'deleteMembers'])->name('delete_members');
});

// plugin
Route::prefix('plugin')->as('plugin.')->middleware(['auth'])->group(function () {
    Route::get('/index', [PluginController::class, 'index'])->middleware(['permission:view_plugin'])->name('plugin_index');
    Route::post('/set-default-plugin', [PluginController::class, 'setDefaultPlugin'])->name('set_default_plugin');
    Route::post('/update-plugin-files', [PluginController::class, 'updatePlugin']);
    Route::post('/bulk-update-plugin', [PluginController::class, 'bulkUpdatePlugin'])->name('bulk_update_plugin');

    Route::get('/get-plugin-details', [DashboardController::class, 'getPluginDetails']);
    Route::post('/active-or-deactive', [DashboardController::class, 'activeOrDeactive']);
    Route::get('/delete', [DashboardController::class, 'delete']);
});