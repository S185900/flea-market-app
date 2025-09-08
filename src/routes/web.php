<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\ProfileController;

Route::post('/register', [RegisterController::class, 'store'])
    ->middleware(['guest:' . config('fortify.guard')])
    ->name('register');

Route::post('/login', [RegisteredUserController::class, 'store'])->name('login');

Route::middleware('auth')->group(function () {
    Route::get('/', [AuthController::class, 'index']);
});

Route::get('/profile', [ProfileController::class, 'index'])->name('profile_index');

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('layouts.app_simple');
});

Route::get('/register', function () {
    return view('auth.register');
});
