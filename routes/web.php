<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProjectController;
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
    Route::get('/', [DashboardController::class, 'index'])->name('home');
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
    Route::get('/profile', [UserController::class, 'profile'])->name('dashboard');
    Route::post('/update-profile/{user}', [UserController::class, 'updateProfile'])->name('update_profile');
});

// projects url
Route::prefix('projects')->as('projects.')->middleware(['auth', '2fa'])->group(function () {
    Route::get('/index', [ProjectController::class, 'index'])->name('project_index');
    Route::post('/datatable', [ProjectController::class, 'datatable'])->name('project_datatable');
});

Route::post('/add-website', [ProjectController::class, 'addWebsite'])->name('add_website');